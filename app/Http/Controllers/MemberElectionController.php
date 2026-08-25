<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Position;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberElectionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();

        // Get all elections
        $elections = Election::withCount('positions')
            ->orderBy('start_time', 'desc')
            ->get();

        // Attach whether user has voted in each
        foreach ($elections as $election) {
            $election->has_voted = $election->hasUserVoted($user);
        }

        return view('member.elections.index', compact('elections'));
    }

    public function show(Election $election)
    {
        $user = auth()->user();

        // Check if the election is active
        if (!$election->isActive()) {
            return redirect()->route('member.elections.index')
                ->with('error', 'This election is not currently active for voting.');
        }

        // Check if user has already voted
        if ($election->hasUserVoted($user)) {
            return redirect()->route('member.elections.index')
                ->with('error', 'You have already cast your vote in this election.');
        }

        $election->load('positions.candidates');

        return view('member.elections.show', compact('election'));
    }

    public function store(Request $request, Election $election)
    {
        $user = auth()->user();

        // Check active and voted status
        if (!$election->isActive()) {
            return redirect()->route('member.elections.index')
                ->with('error', 'This election is not currently active.');
        }

        if ($election->hasUserVoted($user)) {
            return redirect()->route('member.elections.index')
                ->with('error', 'You have already cast your vote in this election.');
        }

        // Validate votes
        // Input format: votes[position_id] = candidate_id
        $request->validate([
            'votes' => 'required|array',
            'votes.*' => 'required|integer|exists:candidates,id',
        ]);

        $submittedVotes = $request->input('votes');
        $positions = $election->positions()->pluck('id')->toArray();

        // Verify that votes are cast for positions belonging to this election
        foreach (array_keys($submittedVotes) as $posId) {
            if (!in_array($posId, $positions)) {
                return redirect()->back()
                    ->with('error', 'Invalid position submitted.');
            }
        }

        // Optional: Ensure they voted for all positions (or we can let them submit partial, but let's enforce all positions have a vote for absolute compliance unless it's empty)
        if (count($submittedVotes) !== count($positions)) {
            return redirect()->back()
                ->with('error', 'Please make a selection for all available positions.');
        }

        // Perform voting in a transaction
        DB::beginTransaction();
        try {
            foreach ($submittedVotes as $posId => $candId) {
                // Double check candidate belongs to position
                $candidateExists = Candidate::where('id', $candId)
                    ->where('position_id', $posId)
                    ->exists();

                if (!$candidateExists) {
                    throw new \Exception('Candidate does not match position.');
                }

                Vote::create([
                    'election_id' => $election->id,
                    'position_id' => $posId,
                    'candidate_id' => $candId,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return redirect()->route('member.elections.index')
                ->with('success', 'Thank you! Your vote has been cast and recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while saving your vote: ' . $e->getMessage());
        }
    }

    /**
     * Show election results to members after it is completed, or even during active if wanted.
     * We'll allow members to see results only for completed elections to ensure fairness.
     */
    public function results(Election $election)
    {
        if (!$election->isCompleted()) {
            return redirect()->route('member.elections.index')
                ->with('error', 'Election results are only available once the election is completed.');
        }

        $election->load(['positions.candidates' => function ($query) {
            $query->withCount('votes');
        }]);

        return view('member.elections.results', compact('election'));
    }
}
