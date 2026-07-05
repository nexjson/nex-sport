<?php

use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\OrganizerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\MatchController;
use App\Http\Controllers\Player\RegistrationController;
use App\Http\Controllers\Player\RewardClaimController;
use App\Http\Controllers\Player\SquadController;
use App\Http\Controllers\Player\TeamController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin & Super Admin actions
    Route::middleware(['role:super-admin,admin'])->group(function () {
        // User Management
        Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::patch('admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('admin/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');

        // Games Management
        Route::get('admin/games', [GameController::class, 'index'])->name('admin.games.index');
        Route::post('admin/games', [GameController::class, 'store'])->name('admin.games.store');
        Route::patch('admin/games/{game}', [GameController::class, 'update'])->name('admin.games.update');
        Route::delete('admin/games/{game}', [GameController::class, 'destroy'])->name('admin.games.destroy');
        Route::post('admin/games/{game}/roles', [GameController::class, 'storeRole'])->name('admin.games.roles.store');
        Route::delete('admin/games/roles/{gameRole}', [GameController::class, 'destroyRole'])->name('admin.games.roles.destroy');

        // Organizer Management
        Route::get('admin/organizers', [OrganizerController::class, 'index'])->name('admin.organizers.index');
        Route::post('admin/organizers', [OrganizerController::class, 'store'])->name('admin.organizers.store');
        Route::patch('admin/organizers/{organizer}', [OrganizerController::class, 'update'])->name('admin.organizers.update');
        Route::delete('admin/organizers/{organizer}', [OrganizerController::class, 'destroy'])->name('admin.organizers.destroy');

        // Payment & Service Fee Management
        Route::get('admin/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('admin/payments/{id}/verify', [AdminPaymentController::class, 'verify'])->name('admin.payments.verify');
        Route::post('admin/payments/fee-config', [AdminPaymentController::class, 'updateFeeConfig'])->name('admin.payments.fee-config');
    });

    // Organizer Actions
    Route::middleware(['role:organizer,super-admin,admin'])->group(function () {
        Route::get('organizer/events', [EventController::class, 'index'])->name('organizer.events.index');
        Route::get('organizer/events/create', [EventController::class, 'create'])->name('organizer.events.create');
        Route::post('organizer/events', [EventController::class, 'store'])->name('organizer.events.store');
        Route::get('organizer/events/{id}/edit', [EventController::class, 'edit'])->name('organizer.events.edit');
        Route::patch('organizer/events/{id}', [EventController::class, 'update'])->name('organizer.events.update');
        Route::delete('organizer/events/{id}', [EventController::class, 'destroy'])->name('organizer.events.destroy');

        Route::post('organizer/events/{id}/toggle-registration', [EventController::class, 'toggleRegistration'])->name('organizer.events.toggle-registration');
        Route::post('organizer/events/{id}/status', [EventController::class, 'updateStatus'])->name('organizer.events.status');
        Route::post('organizer/events/{eventId}/games', [EventController::class, 'storeGame'])->name('organizer.events.games.store');
        Route::delete('organizer/events/{eventId}/games/{eventGamesId}', [EventController::class, 'destroyGame'])->name('organizer.events.games.destroy');
        Route::post('organizer/events/{eventId}/sponsors', [EventController::class, 'storeSponsor'])->name('organizer.events.sponsors.store');
        Route::post('organizer/events/{eventId}/pay', [EventController::class, 'payDeposit'])->name('organizer.events.pay');

        // Match Management & Brackets
        Route::get('organizer/matches/{eventGamesId}', [MatchController::class, 'index'])->name('organizer.matches.index');
        Route::post('organizer/matches/{eventGamesId}/generate', [MatchController::class, 'generate'])->name('organizer.matches.generate');
        Route::post('organizer/matches/{matchId}/score', [MatchController::class, 'updateScore'])->name('organizer.matches.score');
        Route::post('organizer/matches/{matchId}/schedule', [MatchController::class, 'updateSchedule'])->name('organizer.matches.schedule');
        Route::post('organizer/matches/{matchId}/status', [MatchController::class, 'toggleMatchStatus'])->name('organizer.matches.status');
    });

    // Player Actions
    Route::middleware(['role:player,super-admin,admin'])->group(function () {
        // Teams CRUD
        Route::get('player/teams', [TeamController::class, 'index'])->name('player.teams.index');
        Route::post('player/teams', [TeamController::class, 'store'])->name('player.teams.store');
        Route::patch('player/teams/{team}', [TeamController::class, 'update'])->name('player.teams.update');
        Route::delete('player/teams/{team}', [TeamController::class, 'destroy'])->name('player.teams.destroy');

        // Squads CRUD & Transfers
        Route::get('player/squads', [SquadController::class, 'index'])->name('player.squads.index');
        Route::post('player/squads', [SquadController::class, 'store'])->name('player.squads.store');
        Route::patch('player/squads/{id}', [SquadController::class, 'update'])->name('player.squads.update');
        Route::delete('player/squads/{id}', [SquadController::class, 'destroy'])->name('player.squads.destroy');

        Route::post('player/squads/requests', [SquadController::class, 'sendRequest'])->name('player.squads.requests.store');
        Route::post('player/squads/requests/{id}/handle', [SquadController::class, 'handleRequest'])->name('player.squads.requests.handle');
        Route::post('player/squads/{squadId}/release/{playerId}', [SquadController::class, 'releasePlayer'])->name('player.squads.release');

        // Registrations & Tickets
        Route::get('player/registrations', [RegistrationController::class, 'index'])->name('player.registrations.index');
        Route::post('player/registrations', [RegistrationController::class, 'store'])->name('player.registrations.store');
        Route::post('player/registrations/{id}/pay', [RegistrationController::class, 'payTicket'])->name('player.registrations.pay');
        Route::post('player/registrations/{id}/cancel', [RegistrationController::class, 'cancel'])->name('player.registrations.cancel');
        Route::post('player/registrations/{id}/process', [RegistrationController::class, 'processRegistration'])->name('player.registrations.process');

        // Reward Payout Claims
        Route::get('player/claims', [RewardClaimController::class, 'index'])->name('player.claims.index');
        Route::post('player/claims/{id}/claim', [RewardClaimController::class, 'claim'])->name('player.claims.claim');
    });
});

require __DIR__.'/settings.php';
