<?php

namespace App\Repositories\Contracts;

use App\Models\RewardClaim;
use Illuminate\Database\Eloquent\Collection;

interface RewardClaimRepositoryInterface
{
    /**
     * Get all claims.
     *
     * @return Collection<int, RewardClaim>
     */
    public function all(): Collection;

    /**
     * Find claim by id.
     */
    public function find(int $id): ?RewardClaim;

    /**
     * Create claim.
     */
    public function create(array $data): RewardClaim;

    /**
     * Update claim.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete claim.
     */
    public function delete(int $id): bool;

    /**
     * Get claims filed by a user.
     *
     * @return Collection<int, RewardClaim>
     */
    public function getByClaimer(int $userId): Collection;
}
