<?php

namespace Tests\Feature;

use App\Enums\EventPaymentStatus;
use App\Enums\EventStatus;
use App\Enums\MatchStatus;
use App\Enums\SquadStatus;
use App\Enums\TournamentType;
use App\Models\Event;
use App\Models\EventGame;
use App\Models\EventPayment;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\Organizer;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Squad;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed roles
    $this->superAdminRole = Role::firstOrCreate(['name' => 'super-admin'], ['status' => true]);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['status' => true]);
    $this->organizerRole = Role::firstOrCreate(['name' => 'organizer'], ['status' => true]);
    $this->playerRole = Role::firstOrCreate(['name' => 'player'], ['status' => true]);

    $this->game = Game::create([
        'name' => 'Mobile Legends',
        'category' => 'esport',
        'status' => true,
    ]);

    // Create users
    $this->superAdminUser = User::factory()->create(['role_id' => $this->superAdminRole->id, 'status' => true]);
    $this->adminUser = User::factory()->create(['role_id' => $this->adminRole->id, 'status' => true]);
    $this->organizerUser = User::factory()->create(['role_id' => $this->organizerRole->id, 'status' => true]);
    $this->playerUser1 = User::factory()->create(['role_id' => $this->playerRole->id, 'status' => true]);
    $this->playerUser2 = User::factory()->create(['role_id' => $this->playerRole->id, 'status' => true]);

    $this->organizer = Organizer::create([
        'user_id' => $this->organizerUser->id,
        'name' => 'Organizer Corp',
        'status' => true,
    ]);

    $this->team = Team::create([
        'user_id' => $this->playerUser1->id,
        'name' => 'EVOS Esports',
        'short_name' => 'EVOS',
        'status' => true,
    ]);

    $this->squad = Squad::create([
        'team_id' => $this->team->id,
        'game_id' => $this->game->id,
        'name' => 'EVOS MLBB',
        'short_name' => 'EVOSML',
        'status' => SquadStatus::Active,
    ]);
});

test('admin can update any squad and super admin can delete it', function () {
    // 1. Admin updates squad name
    $this->actingAs($this->adminUser);

    $response = $this->patch(route('player.squads.update', $this->squad->id), [
        'name' => 'EVOS Glory',
    ]);
    $response->assertRedirect(route('player.squads.index'));
    $this->assertDatabaseHas('squads', ['id' => $this->squad->id, 'name' => 'EVOS Glory']);

    // 2. Player (non-owner) tries to delete and gets 403
    $this->actingAs($this->playerUser2);
    $response = $this->delete(route('player.squads.destroy', $this->squad->id));
    $response->assertStatus(403);

    // 3. Super Admin deletes squad successfully
    $this->actingAs($this->superAdminUser);
    $response = $this->delete(route('player.squads.destroy', $this->squad->id));
    $response->assertRedirect(route('player.squads.index'));
    $this->assertDatabaseMissing('squads', ['id' => $this->squad->id]);
});

test('event store and update supports location and tournament type', function () {
    $this->actingAs($this->organizerUser);

    // Create event
    $response = $this->post(route('organizer.events.store'), [
        'name' => 'Nex Tourney 2026',
        'description' => 'Great sports event',
        'location' => 'Jakarta Arena',
        'tournament_type' => 'round_robin',
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
    ]);

    $event = Event::where('name', 'Nex Tourney 2026')->first();
    expect($event->location)->toBe('Jakarta Arena');
    expect($event->tournament_type)->toBe(TournamentType::RoundRobin);

    // Update event
    $response = $this->patch(route('organizer.events.update', $event->id), [
        'name' => 'Nex Tourney 2026 Updated',
        'description' => 'Updated Description',
        'location' => 'Online',
        'tournament_type' => 'single_elimination',
        'start_date' => now()->addDays(5)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
    ]);

    $event->refresh();
    expect($event->location)->toBe('Online');
    expect($event->tournament_type)->toBe(TournamentType::SingleElimination);
});

test('admin can toggle registration and override status', function () {
    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'Status Override Test',
        'tournament_type' => TournamentType::SingleElimination,
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(3),
        'registration_start' => now(),
        'registration_end' => now()->addDay(),
        'status' => EventStatus::Registration,
    ]);

    // 1. Organizer toggle registration to ongoing
    $this->actingAs($this->organizerUser);
    $response = $this->post(route('organizer.events.toggle-registration', $event->id));
    $event->refresh();
    expect($event->status)->toBe(EventStatus::Ongoing);

    // 2. Admin overrides status back to draft
    $this->actingAs($this->adminUser);
    $response = $this->post(route('organizer.events.status', $event->id), [
        'status' => 'draft',
    ]);
    $event->refresh();
    expect($event->status)->toBe(EventStatus::Draft);
});

test('admin can update match schedule and guard match status', function () {
    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'Match Management Test',
        'tournament_type' => TournamentType::SingleElimination,
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(3),
        'registration_start' => now(),
        'registration_end' => now()->addDay(),
        'status' => EventStatus::Ongoing,
    ]);

    $eventGame = EventGame::create([
        'event_id' => $event->id,
        'game_id' => $this->game->id,
        'max_squads' => 8,
        'ticket_price' => 0,
        'admin_ticket_fee' => 0,
    ]);

    $match = GameMatch::create([
        'event_games_id' => $eventGame->id,
        'round' => 1,
        'match_order' => 0,
        'squad_home_id' => $this->squad->id,
        'squad_away_id' => null,
        'status' => MatchStatus::Scheduled,
    ]);

    // 1. Admin sets schedule
    $this->actingAs($this->adminUser);
    $response = $this->post(route('organizer.matches.schedule', $match->id), [
        'scheduled_at' => now()->addDays(3)->toDateTimeString(),
    ]);
    $match->refresh();
    expect($match->scheduled_at)->not->toBeNull();

    // 2. Set match to completed
    $match->update(['status' => MatchStatus::Completed]);

    // 3. Organizer tries to revert completed status to live -> rejected
    $this->actingAs($this->organizerUser);
    $response = $this->post(route('organizer.matches.status', $match->id), [
        'status' => 'live',
    ]);
    $response->assertSessionHas('error', 'Only a Super Admin can revert a completed match.');
    $match->refresh();
    expect($match->status)->toBe(MatchStatus::Completed);

    // 4. Super Admin reverts to live successfully
    $this->actingAs($this->superAdminUser);
    $response = $this->post(route('organizer.matches.status', $match->id), [
        'status' => 'live',
    ]);
    $match->refresh();
    expect($match->status)->toBe(MatchStatus::Live);
});

test('admin can verify deposit payments and edit fee configs', function () {
    $event = Event::create([
        'organizer_id' => $this->organizer->id,
        'name' => 'Payment Verification Test',
        'tournament_type' => TournamentType::SingleElimination,
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(3),
        'registration_start' => now(),
        'registration_end' => now()->addDay(),
        'status' => EventStatus::WaitingPayment,
    ]);

    $payment = EventPayment::create([
        'event_id' => $event->id,
        'amount' => 1100000,
        'service_fee' => 100000,
        'status' => EventPaymentStatus::Pending,
    ]);

    $this->actingAs($this->adminUser);

    // 1. Get payments page
    $response = $this->get(route('admin.payments.index'));
    $response->assertStatus(200);

    // 2. Approve payment
    $response = $this->post(route('admin.payments.verify', $payment->id), [
        'action' => 'approved',
    ]);
    $payment->refresh();
    $event->refresh();
    expect($payment->status)->toBe(EventPaymentStatus::Approved);
    expect($event->status)->toBe(EventStatus::Registration);

    // 3. Update service fee config
    $response = $this->post(route('admin.payments.fee-config'), [
        'configs' => [
            ['min_reward' => 0, 'max_reward' => 1000000, 'service_fee' => 75000],
            ['min_reward' => 1000001, 'max_reward' => 5000000, 'service_fee' => 150000],
        ],
    ]);
    $response->assertSessionHas('success', 'Service fee configurations updated successfully.');
    $this->assertDatabaseHas('service_fee_configs', ['service_fee' => 75000]);
});

test('dashboard stats are cached', function () {
    $this->actingAs($this->adminUser);

    Cache::shouldReceive('remember')
        ->once()
        ->with('dashboard_admin', 600, \Closure::class)
        ->andReturn([]);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});
