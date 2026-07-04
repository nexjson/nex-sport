<?php

namespace App\Repositories\Contracts;

use App\Models\Squad;
use Illuminate\Database\Eloquent\Collection;

interface SquadRepositoryInterface
{
    /**
     * Get all squads.
     *
     * @return Collection<int, Squad>
     */
    public function all(): Collection;

    /**
     * Find squad by id.
     */
    public function find(int $id): ?Squad;

    /**
     * Create squad.
     */
    public function create(array $data): Squad;

    /**
     * Update squad.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete squad.
     */
    public function delete(int $id): bool;

    /**
     * Get squads belonging to a team.
     *
     * @return Collection<int, Squad>
     */
    public function getByTeam(int $teamId): Collection;
}
