<?php

namespace App\Repositories\Eloquent;

use App\Models\GameMatch;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MatchRepository implements MatchRepositoryInterface
{
    /**
     * Get all matches.
     *
     * @return Collection<int, GameMatch>
     */
    public function all(): Collection
    {
        return GameMatch::with(['eventGame.event', 'squadHome.team', 'squadAway.team', 'winner.team'])->get();
    }

    /**
     * Find match by id.
     */
    public function find(int $id): ?GameMatch
    {
        return GameMatch::with(['eventGame.event', 'squadHome.team', 'squadAway.team', 'winner.team'])->find($id);
    }

    /**
     * Create match.
     */
    public function create(array $data): GameMatch
    {
        return GameMatch::create($data);
    }

    /**
     * Update match.
     */
    public function update(int $id, array $data): bool
    {
        $match = GameMatch::find($id);

        if ($match) {
            return $match->update($data);
        }

        return false;
    }

    /**
     * Delete match.
     */
    public function delete(int $id): bool
    {
        $match = GameMatch::find($id);

        if ($match) {
            return $match->delete();
        }

        return false;
    }

    /**
     * Get matches for a specific event game division.
     *
     * @return Collection<int, GameMatch>
     */
    public function getByEventGame(int $eventGamesId): Collection
    {
        return GameMatch::where('event_games_id', $eventGamesId)
            ->with(['squadHome.team', 'squadAway.team', 'winner.team'])
            ->orderBy('round')
            ->orderBy('scheduled_at')
            ->get();
    }

    /**
     * Get matches for a squad (either home or away).
     *
     * @return Collection<int, GameMatch>
     */
    public function getMatchesBySquad(int $squadId): Collection
    {
        return GameMatch::where(fn ($query) => $query->where('squad_home_id', $squadId)->orWhere('squad_away_id', $squadId))
            ->with(['eventGame.event', 'eventGame.game', 'squadHome.team', 'squadAway.team', 'winner.team'])
            ->orderBy('scheduled_at')
            ->get();
    }
}
