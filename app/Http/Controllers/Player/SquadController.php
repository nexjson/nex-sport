<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use App\Models\SquadRequest;
use App\Models\Team;
use App\Models\TransferHistory;
use App\Repositories\Contracts\SquadRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SquadController extends Controller
{
    public function __construct(
        protected SquadRepositoryInterface $squadRepository
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $userId = auth()->id();

        // Squads managed by user (via owned teams)
        $managedSquads = $this->squadRepository->all()->filter(function ($squad) use ($userId) {
            return $squad->team->user_id === $userId;
        });

        // Squads where user is a player member
        $memberSquads = $this->squadRepository->all()->filter(function ($squad) use ($userId) {
            return $squad->players->contains('user_id', $userId) && $squad->team->user_id !== $userId;
        });

        // Get player profiles owned by user
        $myPlayers = Player::where('user_id', $userId)->with(['squad', 'game'])->get();

        // Get active invitations/applications for the user's players
        $playerIds = $myPlayers->pluck('id')->toArray();
        $incomingRequests = SquadRequest::whereIn('player_id', $playerIds)
            ->where('type', 'invite')
            ->where('status', 'pending')
            ->with(['squad.team', 'player'])
            ->get();

        // Get active applications for the squads managed by the user
        $managedSquadIds = $managedSquads->pluck('id')->toArray();
        $incomingApplications = SquadRequest::whereIn('squad_id', $managedSquadIds)
            ->where('type', 'apply')
            ->where('status', 'pending')
            ->with(['player.gameRole', 'squad'])
            ->get();

        // All active squads for application
        $allSquadsForApplication = $this->squadRepository->all();

        return Inertia::render('player/squads/Index', [
            'managedSquads' => $managedSquads->values(),
            'memberSquads' => $memberSquads->values(),
            'myPlayers' => $myPlayers,
            'incomingRequests' => $incomingRequests,
            'incomingApplications' => $incomingApplications,
            'allSquads' => $allSquadsForApplication->values(),
            'myTeams' => Team::where('user_id', $userId)->get(),
            'games' => Game::where('status', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255|unique:squads',
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $team = Team::find($validated['team_id']);
        if ($team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated['status'] = true;

        $this->squadRepository->create($validated);

        return redirect()->route('player.squads.index')->with('success', 'Squad division created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $squad = $this->squadRepository->find($id);

        if (! $squad) {
            abort(404);
        }

        if ($squad->team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:squads,name,'.$id,
            'logo' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $this->squadRepository->update($id, $validated);

        return redirect()->route('player.squads.index')->with('success', 'Squad division updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $squad = $this->squadRepository->find($id);

        if (! $squad) {
            abort(404);
        }

        if ($squad->team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Release players & log transfers
        foreach ($squad->players as $player) {
            $oldSquadId = $player->squad_id;
            $player->update(['squad_id' => null]);

            TransferHistory::create([
                'player_id' => $player->id,
                'from_squad_id' => $oldSquadId,
                'to_squad_id' => null,
                'transfer_type' => 'disband',
                'transfer_date' => now(),
            ]);
        }

        $this->squadRepository->delete($id);

        return redirect()->route('player.squads.index')->with('success', 'Squad division disbanded successfully.');
    }

    /**
     * Send an invite or application.
     */
    public function sendRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'squad_id' => 'required|exists:squads,id',
            'player_id' => 'required|exists:players,id',
            'type' => 'required|in:invite,apply',
            'notes' => 'nullable|string',
        ]);

        $squad = $this->squadRepository->find($validated['squad_id']);
        $player = Player::find($validated['player_id']);

        // Check if game matches
        if ($squad->game_id !== $player->game_id) {
            return redirect()->back()->with('error', 'Player game type does not match squad game type.');
        }

        // Check roster duplication
        if ($player->squad_id === $squad->id) {
            return redirect()->back()->with('error', 'Player is already a member of this squad.');
        }

        // Limit check
        $exists = SquadRequest::where('squad_id', $squad->id)
            ->where('player_id', $player->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'A pending request already exists.');
        }

        SquadRequest::create($validated);

        return redirect()->route('player.squads.index')->with('success', 'Request sent successfully.');
    }

    /**
     * Process an invite or application (approve/reject/cancel).
     */
    public function handleRequest(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,cancel',
        ]);

        $squadRequest = SquadRequest::with(['squad.team', 'player'])->find($id);

        if (! $squadRequest) {
            abort(404);
        }

        $isLeader = $squadRequest->squad->team->user_id === auth()->id();
        $isPlayer = $squadRequest->player->user_id === auth()->id();

        if ($validated['action'] === 'approve') {
            // Leader approves application OR Player accepts invitation
            if (($squadRequest->type === 'apply' && ! $isLeader) || ($squadRequest->type === 'invite' && ! $isPlayer)) {
                abort(403, 'Unauthorized.');
            }

            // Check if player is already in a squad for this game
            if ($squadRequest->player->squad_id !== null) {
                return redirect()->back()->with('error', 'Player is already in another squad.');
            }

            // Max players check (e.g. 10)
            if ($squadRequest->squad->players()->count() >= 10) {
                return redirect()->back()->with('error', 'Squad roster is full (max 10 players).');
            }

            // Update player's squad membership
            $player = $squadRequest->player;
            $oldSquadId = $player->squad_id;
            $player->update(['squad_id' => $squadRequest->squad_id]);

            // Log transfer
            TransferHistory::create([
                'player_id' => $player->id,
                'from_squad_id' => $oldSquadId,
                'to_squad_id' => $squadRequest->squad_id,
                'transfer_type' => 'join',
                'transfer_date' => now(),
            ]);

            $squadRequest->update(['status' => 'approved']);

            return redirect()->route('player.squads.index')->with('success', 'Squad request approved.');
        }

        if ($validated['action'] === 'reject') {
            if (($squadRequest->type === 'apply' && ! $isLeader) || ($squadRequest->type === 'invite' && ! $isPlayer)) {
                abort(403, 'Unauthorized.');
            }

            $squadRequest->update(['status' => 'rejected']);

            return redirect()->route('player.squads.index')->with('success', 'Squad request rejected.');
        }

        if ($validated['action'] === 'cancel') {
            if (($squadRequest->type === 'apply' && ! $isPlayer) || ($squadRequest->type === 'invite' && ! $isLeader)) {
                abort(403, 'Unauthorized.');
            }

            $squadRequest->update(['status' => 'cancelled']);

            return redirect()->route('player.squads.index')->with('success', 'Squad request cancelled.');
        }

        return redirect()->back();
    }

    /**
     * Kick/release a player.
     */
    public function releasePlayer(Request $request, int $squadId, int $playerId): RedirectResponse
    {
        $squad = $this->squadRepository->find($squadId);
        $player = Player::find($playerId);

        if (! $squad || ! $player) {
            abort(404);
        }

        if ($squad->team->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($player->squad_id !== $squad->id) {
            return redirect()->back()->with('error', 'Player is not in this squad.');
        }

        $player->update(['squad_id' => null]);

        TransferHistory::create([
            'player_id' => $player->id,
            'from_squad_id' => $squad->id,
            'to_squad_id' => null,
            'transfer_type' => 'release',
            'transfer_date' => now(),
        ]);

        return redirect()->route('player.squads.index')->with('success', 'Player released from roster.');
    }
}
