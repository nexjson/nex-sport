<?php

namespace App\Repositories\Eloquent;

use App\Models\RewardClaim;
use App\Repositories\Contracts\RewardClaimRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RewardClaimRepository implements RewardClaimRepositoryInterface
{
    /**
     * Get all claims.
     *
     * @return Collection<int, RewardClaim>
     */
    public function all(): Collection
    {
        return RewardClaim::with(['reward.eventGame.event', 'claimedBy'])->get();
    }

    /**
     * Find claim by id.
     */
    public function find(int $id): ?RewardClaim
    {
        return RewardClaim::with(['reward.eventGame.event', 'claimedBy'])->find($id);
    }

    /**
     * Create claim.
     */
    public function create(array $data): RewardClaim
    {
        return RewardClaim::create($data);
    }

    /**
     * Update claim.
     */
    public function update(int $id, array $data): bool
    {
        $claim = RewardClaim::find($id);

        if ($claim) {
            return $claim->update($data);
        }

        return false;
    }

    /**
     * Delete claim.
     */
    public function delete(int $id): bool
    {
        $claim = RewardClaim::find($id);

        if ($claim) {
            return $claim->delete();
        }

        return false;
    }

    /**
     * Get claims filed by a user.
     *
     * @return Collection<int, RewardClaim>
     */
    public function getByClaimer(int $userId): Collection
    {
        return RewardClaim::where('claimed_by_id', $userId)
            ->with(['reward.eventGame.event', 'reward.eventGame.game'])
            ->get();
    }
}
