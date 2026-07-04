<?php

namespace App\Repositories\Contracts;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Collection;

interface RegistrationRepositoryInterface
{
    /**
     * Get all registrations.
     *
     * @return Collection<int, Registration>
     */
    public function all(): Collection;

    /**
     * Find registration by id.
     */
    public function find(int $id): ?Registration;

    /**
     * Create registration.
     */
    public function create(array $data): Registration;

    /**
     * Update registration.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete registration.
     */
    public function delete(int $id): bool;

    /**
     * Get registrations for a specific event game division.
     *
     * @return Collection<int, Registration>
     */
    public function getByEventGame(int $eventGamesId): Collection;

    /**
     * Get registrations for a squad.
     *
     * @return Collection<int, Registration>
     */
    public function getBySquad(int $squadId): Collection;
}
