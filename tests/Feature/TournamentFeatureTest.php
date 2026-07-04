<?php

namespace Tests\Feature;

use App\Enums\RegistrationPaymentStatus;
use App\Enums\SquadStatus;
use App\Models\Event;
use App\Models\EventGame;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\GameRole;
use App\Models\Organizer;
use App\Models\Player;
use App\Models\Registration;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\Role;
use App\Models\Squad;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles
    $this->superAdminRole = Role::firstOrCreate(['name' => 'super-admin'], ['status' => true]);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['status' => true]);
    $this->organizerRole = Role::firstOrCreate(['name' => 'organizer'], ['status' => true]);
    $this->playerRole = Role::firstOrCreate(['name' => 'player'], ['status' => true]);

    // Create Game & Roles
    $this->game = Game::create([
        'name' => 'Mobile Legends',
        'category' => 'esport',
        'status' => true,
    ]);

    $this->gameRole = GameRole::create([
        'game_id' => $this->game->id,
        'name' => 'Roamer',
        'status' => true,
    ]);

    // Create users
    $this->organizerUser = User::factory()->create([
        'role_id' => $this->organizerRole->id,
        'status' => true,
    ]);

    $this->organizer = Organizer::create([
        'user_id' => $this->organizerUser->id,
        'name' => 'Organizer Corp',
        'status' => true,
    ]);

    $this->playerUser = User::factory()->create([
        'role_id' => $this->playerRole->id,
        'status' => true,
    ]);

    // Create main Player Profile
    $this->playerProfile = Player::create([
        'user_id' => $this->playerUser->id,
        'name' => 'Roster Hero',
        'nickname' => 'HeroOne',
        'game_role_id' => $this->gameRole->id,
        'game_id' => $this->game->id,
    ]);
});

test('player can CRUD team organizations', function () {
    $this->actingAs($this->playerUser);

    // Create
    $response = $this->post(route('player.teams.store'), [
        'name' => 'RRQ Esports',
        'short_name' => 'RRQ',
        'description' => 'Kings of Kings',
    ]);
    $response->assertRedirect(route('player.teams.index'));
    $this->assertDatabaseHas('teams', ['name' => 'RRQ Esports']);

    $team = Team::where('name', 'RRQ Esports')->first();

    // Update
    $response = $this->patch(route('player.teams.update', $team->id), [
        'name' => 'RRQ Hoshi',
        'short_name' => 'RRQH',
        'description' => 'Updated Description',
    ]);
    $response->assertRedirect(route('player.teams.index'));
    $this->assertDatabaseHas('teams', ['name' => 'RRQ Hoshi']);

    // Delete
    $response = $this->delete(route('player.teams.destroy', $team->id));
    $response->assertRedirect(route('player.teams.index'));
    $this->assertDatabaseMissing('teams', ['id' => $team->id]);
});

test('roster application fails if game type does not match squad game type', function () {
    $this->actingAs($this->playerUser);

    $otherGame = Game::create([
        'name' => 'Football',
        'category' => 'sport',
        'status' => true,
    ]);

    $team = Team::create([
        'name' => 'EVOS Team',
        'short_name' => 'EVOS',
        'user_id' => $this->organizerUser->id, // Owned by organizer
        'status' => true,
    ]);

    $squad = Squad::create([
        'team_id' => $team->id,
        'game_id' => $otherGame->id, // Squad is for Football
        'name' => 'EVOS Football',
        'short_name' => 'EVOSFT',
        'status' => SquadStatus::Active,
    ]);

    // Player profile is for Mobile Legends, trying to apply for Football squad
    $response = $this->post(route('player.squads.requests.store'), [
        'squad_id' => $squad->id,
        'player_id' => $this->playerProfile->id,
        'type' => 'apply',
    ]);

    $response->assertSessionHas('error', 'Player game type does not match squad game type.');
    $this->assertDatabaseMissing('squad_requests', [
        'squad_id' => $squad->id,
        'player_id' => $this->playerProfile->id,
    ]);
});

test('bracket generation fails with less than 2 approved squads', function () {
    $this->actingAs($this->organizerUser);

    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'MLBB Tourney',
        'tournament_type' => 'single_elimination',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'registration_start' => now(),
        'registration_end' => now()->addDay(),
        'status' => 'draft',
    ]);

    $eventGame = EventGame::create([
        'event_id' => $event->id,
        'game_id' => $this->game->id,
        'max_squads' => 8,
        'ticket_price' => 0,
        'admin_ticket_fee' => 0,
    ]);

    $response = $this->post(route('organizer.matches.generate', $eventGame->id));
    $response->assertSessionHas('error', 'Need at least 2 approved squads to generate bracket.');
});

test('bracket generates successfully, updates standings, winner promotes, and claim auto-creates', function () {
    // 1. Setup 2 Teams and 2 Squads
    $team1 = Team::create(['name' => 'Team Alpha', 'short_name' => 'TA', 'user_id' => $this->playerUser->id, 'status' => true]);
    $team2 = Team::create(['name' => 'Team Beta', 'short_name' => 'TB', 'user_id' => $this->organizerUser->id, 'status' => true]);

    $squad1 = Squad::create(['team_id' => $team1->id, 'game_id' => $this->game->id, 'name' => 'Squad A', 'short_name' => 'SA', 'status' => SquadStatus::Active]);
    $squad2 = Squad::create(['team_id' => $team2->id, 'game_id' => $this->game->id, 'name' => 'Squad B', 'short_name' => 'SB', 'status' => SquadStatus::Active]);

    // Create Event
    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'NEX-Sport MLBB League',
        'tournament_type' => 'single_elimination',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'registration_start' => now(),
        'registration_end' => now()->addDay(),
        'status' => 'draft',
    ]);

    $eventGame = EventGame::create([
        'event_id' => $event->id,
        'game_id' => $this->game->id,
        'max_squads' => 8,
        'ticket_price' => 0,
        'admin_ticket_fee' => 0,
    ]);

    // Add champion rewards
    $reward = Reward::create([
        'event_games_id' => $eventGame->id,
        'reward_type' => 'PRIZE',
        'tier' => 1,
        'title' => 'Champion Prize',
        'prize_amount' => 500000,
    ]);

    // Register both squads
    Registration::create([
        'squad_id' => $squad1->id,
        'event_games_id' => $eventGame->id,
        'status' => 'approved',
        'payment_status' => RegistrationPaymentStatus::Paid,
        'amount_paid' => 0,
    ]);

    Registration::create([
        'squad_id' => $squad2->id,
        'event_games_id' => $eventGame->id,
        'status' => 'approved',
        'payment_status' => RegistrationPaymentStatus::Paid,
        'amount_paid' => 0,
    ]);

    $this->actingAs($this->organizerUser);

    // 2. Generate bracket
    $response = $this->post(route('organizer.matches.generate', $eventGame->id));
    $response->assertSessionHas('success', 'Bracket generated and tournament started!');

    // Verify match created in Round 1
    $this->assertDatabaseHas('matches', [
        'event_games_id' => $eventGame->id,
        'round' => 1,
        'match_order' => 0,
    ]);

    $match = GameMatch::where('event_games_id', $eventGame->id)->where('round', 1)->first();

    // 3. Update score -> finalize final match
    $response = $this->post(route('organizer.matches.score', $match->id), [
        'squad_home_score' => 3,
        'squad_away_score' => 1,
    ]);

    $response->assertSessionHas('success', 'Score recorded successfully.');

    // Verify match completed and winner set
    $match->refresh();
    expect($match->status->value)->toBe('completed');
    expect($match->winner_id)->toBe($match->squad_home_id);

    // Verify tournament finalized and completed
    $event->refresh();
    expect($event->status->value)->toBe('completed');

    // Verify reward claim created for the champion leader (squad1 owned by playerUser)
    $this->assertDatabaseHas('reward_claims', [
        'reward_id' => $reward->id,
        'claimed_by_id' => $this->playerUser->id,
        'status' => 'pending',
    ]);
});

test('player can claim prize reward and process Mock Disbursement API', function () {
    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'NEX League Completed',
        'tournament_type' => 'single_elimination',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'registration_start' => now(),
        'registration_end' => now(),
        'status' => 'completed',
    ]);

    $eventGame = EventGame::create([
        'event_id' => $event->id,
        'game_id' => $this->game->id,
        'max_squads' => 4,
        'ticket_price' => 0,
        'admin_ticket_fee' => 0,
    ]);

    $reward = Reward::create([
        'event_games_id' => $eventGame->id,
        'reward_type' => 'PRIZE',
        'tier' => 1,
        'title' => 'First Place',
        'prize_amount' => 1000000,
    ]);

    $claim = RewardClaim::create([
        'reward_id' => $reward->id,
        'amount' => 1000000,
        'claimed_by_id' => $this->playerUser->id,
        'status' => 'pending',
    ]);

    $this->actingAs($this->playerUser);

    // Negative case: invalid bank account triggers fail
    $response = $this->post(route('player.claims.claim', $claim->id), [
        'bank_name' => 'BCA',
        'account_number' => '99999', // Triggers mock disbursement failure
        'account_name' => 'Hero Owner',
    ]);

    $response->assertSessionHas('error', 'Disbursement failed. Please verify your bank account details.');
    $claim->refresh();
    expect($claim->status->value)->toBe('failed');

    // Positive case: correct credentials
    $response = $this->post(route('player.claims.claim', $claim->id), [
        'bank_name' => 'BCA',
        'account_number' => '1240055555',
        'account_name' => 'Hero Owner',
    ]);

    $response->assertSessionHas('success', 'Prize payout transfer successful!');
    $claim->refresh();
    expect($claim->status->value)->toBe('paid');
    expect($claim->payment_receipt)->not->toBeNull();
});
