<?php

namespace App\Repositories\Eloquent;

use App\Models\Registration;
use App\Repositories\Contracts\RegistrationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RegistrationRepository implements RegistrationRepositoryInterface
{
    /**
     * Get all registrations.
     *
     * @return Collection<int, Registration>
     */
    public function all(): Collection
    {
        return Registration::with(['squad.team', 'eventGame.event', 'eventGame.game'])->get();
    }

    /**
     * Find registration by id.
     */
    public function find(int $id): ?Registration
    {
        return Registration::with(['squad.team', 'eventGame.event', 'eventGame.game'])->find($id);
    }

    /**
     * Create registration.
     */
    public function create(array $data): Registration
    {
        return Registration::create($data);
    }

    /**
     * Update registration.
     */
    public function update(int $id, array $data): bool
    {
        $registration = Registration::find($id);

        if ($registration) {
            return $registration->update($data);
        }

        return false;
    }

    /**
     * Delete registration.
     */
    public function delete(int $id): bool
    {
        $registration = Registration::find($id);

        if ($registration) {
            return $registration->delete();
        }

        return false;
    }

    /**
     * Get registrations for a specific event game division.
     *
     * @return Collection<int, Registration>
     */
    public function getByEventGame(int $eventGamesId): Collection
    {
        return Registration::where('event_games_id', $eventGamesId)
            ->with(['squad.team'])
            ->get();
    }

    /**
     * Get registrations for a squad.
     *
     * @return Collection<int, Registration>
     */
    public function getBySquad(int $squadId): Collection
    {
        return Registration::where('squad_id', $squadId)
            ->with(['eventGame.event'])
            ->get();
    }
}
