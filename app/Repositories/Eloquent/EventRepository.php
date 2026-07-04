<?php

namespace App\Repositories\Eloquent;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EventRepository implements EventRepositoryInterface
{
    /**
     * Get all events.
     *
     * @return Collection<int, Event>
     */
    public function all(): Collection
    {
        return Event::with(['organizer', 'eventGames.game'])->get();
    }

    /**
     * Find event by id.
     */
    public function find(int $id): ?Event
    {
        return Event::with(['organizer', 'eventGames.game', 'eventSponsors', 'eventPayments'])->find($id);
    }

    /**
     * Create event.
     */
    public function create(array $data): Event
    {
        return Event::create($data);
    }

    /**
     * Update event.
     */
    public function update(int $id, array $data): bool
    {
        $event = Event::find($id);

        if ($event) {
            return $event->update($data);
        }

        return false;
    }

    /**
     * Delete event.
     */
    public function delete(int $id): bool
    {
        $event = Event::find($id);

        if ($event) {
            return $event->delete();
        }

        return false;
    }

    /**
     * Get events managed by organizer.
     *
     * @return Collection<int, Event>
     */
    public function getByOrganizer(int $organizerId): Collection
    {
        return Event::where('organizer_id', $organizerId)->with('eventGames.game')->get();
    }

    /**
     * Get public active events (registration or ongoing status).
     *
     * @return Collection<int, Event>
     */
    public function getPublicEvents(): Collection
    {
        return Event::whereIn('status', [EventStatus::Registration, EventStatus::Ongoing])
            ->with(['organizer', 'eventGames.game'])
            ->get();
    }

    /**
     * Get ongoing live events.
     *
     * @return Collection<int, Event>
     */
    public function getLiveEvents(): Collection
    {
        return Event::where('status', EventStatus::Ongoing)
            ->with(['organizer', 'eventGames.game'])
            ->get();
    }
}
