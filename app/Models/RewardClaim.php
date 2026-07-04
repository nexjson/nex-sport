<?php

namespace App\Models;

use App\Enums\RewardClaimStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $reward_id
 * @property int $amount
 * @property int|null $squad_id
 * @property int|null $player_id
 * @property int $claimed_by_id
 * @property RewardClaimStatus $status
 * @property string|null $payment_method
 * @property string|null $bank_name
 * @property string|null $account_number
 * @property string|null $account_name
 * @property string|null $payment_receipt
 * @property Carbon $claimed_at
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'reward_id', 'amount', 'squad_id', 'player_id', 'claimed_by_id',
    'status', 'payment_method', 'bank_name', 'account_number',
    'account_name', 'payment_receipt', 'claimed_at', 'paid_at',
])]
class RewardClaim extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RewardClaimStatus::class,
            'amount' => 'integer',
            'claimed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the reward being claimed.
     *
     * @return BelongsTo<Reward, $this>
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get the squad that earned the reward.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    /**
     * Get the player that earned the reward.
     *
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the user who made the claim.
     *
     * @return BelongsTo<User, $this>
     */
    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_id');
    }
}
