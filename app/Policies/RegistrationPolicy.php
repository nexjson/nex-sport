<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Registration $registration): bool
    {
        return $registration->squad->team->user_id === $user->id ||
            $registration->eventGame->event->organizer->user_id === $user->id ||
            in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model (pay ticket).
     */
    public function update(User $user, Registration $registration): bool
    {
        return $registration->squad->team->user_id === $user->id;
    }

    /**
     * Determine whether the user can cancel the registration.
     */
    public function cancel(User $user, Registration $registration): bool
    {
        return $registration->squad->team->user_id === $user->id;
    }

    /**
     * Determine whether the user can process (approve/reject) the registration.
     */
    public function processRegistration(User $user, Registration $registration): bool
    {
        return $registration->eventGame->event->organizer->user_id === $user->id ||
            in_array($user->role?->name, ['super-admin', 'admin']);
    }
}
