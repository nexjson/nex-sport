<?php

namespace App\Repositories\Eloquent;

use App\Models\Squad;
use App\Repositories\Contracts\SquadRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SquadRepository implements SquadRepositoryInterface
{
    /**
     * Get all squads.
     *
     * @return Collection<int, Squad>
     */
    public function all(): Collection
    {
        return Squad::with(['team', 'game', 'players.gameRole'])->get();
    }

    /**
     * Find squad by id.
     */
    public function find(int $id): ?Squad
    {
        return Squad::with(['team', 'game', 'players.gameRole'])->find($id);
    }

    /**
     * Create squad.
     */
    public function create(array $data): Squad
    {
        return Squad::create($data);
    }

    /**
     * Update squad.
     */
    public function update(int $id, array $data): bool
    {
        $squad = Squad::find($id);

        if ($squad) {
            return $squad->update($data);
        }

        return false;
    }

    /**
     * Delete squad.
     */
    public function delete(int $id): bool
    {
        $squad = Squad::find($id);

        if ($squad) {
            return $squad->delete();
        }

        return false;
    }

    /**
     * Get squads belonging to a team.
     *
     * @return Collection<int, Squad>
     */
    public function getByTeam(int $teamId): Collection
    {
        return Squad::where('team_id', $teamId)->with('game')->get();
    }
}
