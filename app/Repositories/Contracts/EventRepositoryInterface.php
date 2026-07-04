<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface
{
    /**
     * Get all events.
     *
     * @return Collection<int, Event>
     */
    public function all(): Collection;

    /**
     * Find event by id.
     */
    public function find(int $id): ?Event;

    /**
     * Create event.
     */
    public function create(array $data): Event;

    /**
     * Update event.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete event.
     */
    public function delete(int $id): bool;

    /**
     * Get events managed by organizer.
     *
     * @return Collection<int, Event>
     */
    public function getByOrganizer(int $organizerId): Collection;

    /**
     * Get public active events (registration or ongoing status).
     *
     * @return Collection<int, Event>
     */
    public function getPublicEvents(): Collection;

    /**
     * Get ongoing live events.
     *
     * @return Collection<int, Event>
     */
    public function getLiveEvents(): Collection;
}
