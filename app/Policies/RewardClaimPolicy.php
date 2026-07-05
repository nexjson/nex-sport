<?php

namespace App\Policies;

use App\Models\RewardClaim;
use App\Models\User;

class RewardClaimPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RewardClaim $claim): bool
    {
        return $claim->claimed_by_id === $user->id || in_array($user->role?->name, ['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can claim the reward.
     */
    public function claim(User $user, RewardClaim $claim): bool
    {
        return $claim->claimed_by_id === $user->id;
    }
}
