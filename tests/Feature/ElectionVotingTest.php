<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Election;
use App\Models\Position;
use App\Models\Candidate;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ElectionVotingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@mlsako.com',
            'role' => 'admin',
            'company_id' => '11111111',
            'password' => Hash::make('password'),
        ]);

        $this->member = User::create([
            'name' => 'John Member',
            'email' => 'member@mlsako.com',
            'role' => 'member',
            'company_id' => '22222222',
            'password' => Hash::make('password'),
        ]);

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    /**
     * Test only admins can access admin election routes.
     */
    public function test_only_admins_can_access_admin_election_routes(): void
    {
        // Member should be blocked
        $this->actingAs($this->member)
            ->get('/admin/elections')
            ->assertRedirect('/')
            ->assertSessionHasErrors('login_identifier');

        // Admin should pass
        $this->actingAs($this->admin)
            ->get('/admin/elections')
            ->assertStatus(200);
    }

    /**
     * Test admin can create an election, a position, and candidates.
     */
    public function test_admin_can_manage_election_structure(): void
    {
        $this->actingAs($this->admin);

        // 1. Create election
        $electionData = [
            'name' => 'Annual Board Election',
            'description' => 'Vote for the board directors',
            'start_time' => Carbon::now()->addHour()->toDateTimeString(),
            'end_time' => Carbon::now()->addDays(2)->toDateTimeString(),
        ];

        $response = $this->post('/admin/elections', $electionData);
        $response->assertRedirect('/admin/elections');
        $this->assertDatabaseHas('elections', ['name' => 'Annual Board Election']);

        $election = Election::first();

        // 2. Add position
        $response = $this->post("/admin/elections/{$election->id}/positions", [
            'name' => 'President',
        ]);
        $response->assertRedirect("/admin/elections/{$election->id}");
        $this->assertDatabaseHas('positions', ['name' => 'President', 'election_id' => $election->id]);

        $position = Position::first();

        // 3. Add Candidate
        $response = $this->post("/admin/positions/{$position->id}/candidates", [
            'name' => 'Alice candidate',
        ]);
        $response->assertRedirect("/admin/elections/{$election->id}");
        $this->assertDatabaseHas('candidates', ['name' => 'Alice candidate', 'position_id' => $position->id]);
    }

    /**
     * Test member can view elections and cast votes.
     */
    public function test_member_can_vote_in_active_election(): void
    {
        // 1. Setup active election
        $election = Election::create([
            'name' => 'Management Board',
            'description' => 'Vote for leaders',
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->addHour(),
        ]);

        $position = $election->positions()->create(['name' => 'Treasurer']);
        $candidate1 = $position->candidates()->create(['name' => 'Bob']);
        $candidate2 = $position->candidates()->create(['name' => 'Charlie']);

        // 2. Access elections index
        $this->actingAs($this->member)
            ->get('/elections')
            ->assertStatus(200)
            ->assertSee('Management Board')
            ->assertSee('Cast Your Vote');

        // 3. View ballot page
        $this->get("/elections/{$election->id}")
            ->assertStatus(200)
            ->assertSee('Treasurer')
            ->assertSee('Bob')
            ->assertSee('Charlie');

        // 4. Submit vote
        $votePayload = [
            'votes' => [
                $position->id => $candidate1->id,
            ]
        ];

        $response = $this->post("/elections/{$election->id}/vote", $votePayload);
        $response->assertRedirect('/elections');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('votes', [
            'election_id' => $election->id,
            'position_id' => $position->id,
            'candidate_id' => $candidate1->id,
            'user_id' => $this->member->id,
        ]);

        // 5. Try voting again (should be blocked)
        $this->post("/elections/{$election->id}/vote", $votePayload)
            ->assertRedirect('/elections')
            ->assertSessionHas('error', 'You have already cast your vote in this election.');
    }

    /**
     * Test vote constraints (e.g. inactive election).
     */
    public function test_cannot_vote_in_inactive_election(): void
    {
        // 1. Upcoming election
        $election = Election::create([
            'name' => 'Future Election',
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addHours(2),
        ]);

        $position = $election->positions()->create(['name' => 'Auditor']);
        $candidate = $position->candidates()->create(['name' => 'Dave']);

        $votePayload = [
            'votes' => [
                $position->id => $candidate->id,
            ]
        ];

        $this->actingAs($this->member)
            ->post("/elections/{$election->id}/vote", $votePayload)
            ->assertRedirect('/elections')
            ->assertSessionHas('error', 'This election is not currently active.');
    }

    /**
     * Test layout sidebar exhibits Vote Now! badge when active unvoted election exists,
     * and badge disappears once the vote is cast.
     */
    public function test_layout_exhibits_and_clears_vote_now_badge_appropriately(): void
    {
        // 1. Create an active election
        $election = Election::create([
            'name' => 'Board Selection 2026',
            'start_time' => Carbon::now()->subMinutes(10),
            'end_time' => Carbon::now()->addDay(),
        ]);
        $position = $election->positions()->create(['name' => 'Secretary']);
        $candidate = $position->candidates()->create(['name' => 'Michael']);

        // 2. Access dashboard - should see "Vote Now!" badge in sidebar layout
        $this->actingAs($this->member)
            ->get('/savings')
            ->assertStatus(200)
            ->assertSee('Vote Now!');

        // 3. Vote in the election
        $votePayload = [
            'votes' => [
                $position->id => $candidate->id,
            ]
        ];
        $this->post("/elections/{$election->id}/vote", $votePayload)
            ->assertRedirect('/elections');

        // 4. Access dashboard again - badge should have disappeared
        $this->get('/savings')
            ->assertStatus(200)
            ->assertDontSee('Vote Now!');
    }
}
