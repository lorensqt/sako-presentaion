<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\Position;
use App\Models\Candidate;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminElectionController extends Controller
{
    public function index()
    {
        $elections = Election::withCount('positions')
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('admin.elections.index', compact('elections'));
    }

    public function create()
    {
        return view('admin.elections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        Election::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'upcoming',
        ]);

        return redirect()->route('admin.elections.index')
            ->with('success', 'Election created successfully!');
    }

    public function show(Election $election)
    {
        $election->load(['positions.candidates' => function ($query) {
            $query->withCount('votes');
        }]);

        return view('admin.elections.show', compact('election'));
    }

    public function edit(Election $election)
    {
        return view('admin.elections.edit', compact('election'));
    }

    public function update(Request $request, Election $election)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $election->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.elections.show', $election)
            ->with('success', 'Election details updated successfully!');
    }

    public function destroy(Election $election)
    {
        $election->delete();

        return redirect()->route('admin.elections.index')
            ->with('success', 'Election deleted successfully!');
    }

    public function storePosition(Request $request, Election $election)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $election->positions()->create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.elections.show', $election)
            ->with('success', 'Position added successfully!');
    }

    public function destroyPosition(Position $position)
    {
        $electionId = $position->election_id;
        $position->delete();

        return redirect()->route('admin.elections.show', $electionId)
            ->with('success', 'Position deleted successfully!');
    }

    public function storeCandidate(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        $position->candidates()->create([
            'name' => $request->name,
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('admin.elections.show', $position->election_id)
            ->with('success', 'Candidate added successfully!');
    }

    public function destroyCandidate(Candidate $candidate)
    {
        $electionId = $candidate->position->election_id;
        
        if ($candidate->photo_path) {
            Storage::disk('public')->delete($candidate->photo_path);
        }

        $candidate->delete();

        return redirect()->route('admin.elections.show', $electionId)
            ->with('success', 'Candidate removed successfully!');
    }

    public function results(Election $election)
    {
        $election->load(['positions.candidates' => function ($query) {
            $query->withCount('votes');
        }]);

        // Fetch all individual tracked votes for audit
        $votes = Vote::with(['user', 'position', 'candidate'])
            ->where('election_id', $election->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Calculate some statistics
        $totalVotersCount = \App\Models\User::whereNotIn('role', ['admin', 'super_admin'])->count(); // non-admins
        $votedCount = Vote::where('election_id', $election->id)
            ->distinct('user_id')
            ->count('user_id');

        $turnoutPercentage = $totalVotersCount > 0 ? round(($votedCount / $totalVotersCount) * 100, 2) : 0;

        return view('admin.elections.results', compact('election', 'votes', 'votedCount', 'totalVotersCount', 'turnoutPercentage'));
    }
}
