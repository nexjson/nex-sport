<?php

namespace App\Repositories\Contracts;

use App\Models\GameMatch;
use Illuminate\Database\Eloquent\Collection;

interface MatchRepositoryInterface
{
    /**
     * Get all matches.
     *
     * @return Collection<int, GameMatch>
     */
    public function all(): Collection;

    /**
     * Find match by id.
     */
    public function find(int $id): ?GameMatch;

    /**
     * Create match.
     */
    public function create(array $data): GameMatch;

    /**
     * Update match.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete match.
     */
    public function delete(int $id): bool;

    /**
     * Get matches for a specific event game division.
     *
     * @return Collection<int, GameMatch>
     */
    public function getByEventGame(int $eventGamesId): Collection;

    /**
     * Get matches for a squad (either home or away).
     *
     * @return Collection<int, GameMatch>
     */
    public function getMatchesBySquad(int $squadId): Collection;
}
