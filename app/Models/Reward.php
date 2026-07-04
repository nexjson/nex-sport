<?php

namespace App\Models;

use App\Enums\RewardType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_games_id
 * @property RewardType $reward_type
 * @property int $tier
 * @property string $title
 * @property string|null $description
 * @property int|null $prize_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['event_games_id', 'reward_type', 'tier', 'title', 'description', 'prize_amount'])]
class Reward extends Model
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
            'reward_type' => RewardType::class,
            'tier' => 'integer',
            'prize_amount' => 'integer',
        ];
    }

    /**
     * Get the event game this reward is assigned to.
     *
     * @return BelongsTo<EventGame, $this>
     */
    public function eventGame(): BelongsTo
    {
        return $this->belongsTo(EventGame::class, 'event_games_id');
    }

    /**
     * Get the claims for this reward.
     *
     * @return HasMany<RewardClaim, $this>
     */
    public function rewardClaims(): HasMany
    {
        return $this->hasMany(RewardClaim::class);
    }
}
