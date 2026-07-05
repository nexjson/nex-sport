<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
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
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->organizers()->exists() || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || $user->role?->name === 'super-admin';
    }

    /**
     * Determine whether the user can toggle registration status.
     */
    public function toggleRegistration(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can store a game division in the event.
     */
    public function storeGame(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can destroy a game division in the event.
     */
    public function destroyGame(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can store a sponsor for the event.
     */
    public function storeSponsor(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can pay the deposit.
     */
    public function payDeposit(User $user, Event $event): bool
    {
        return $event->organizer->user_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }
}
