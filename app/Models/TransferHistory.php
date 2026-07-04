<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $player_id
 * @property int|null $from_squad_id
 * @property int|null $to_squad_id
 * @property string $transfer_type
 * @property int|null $transfer_fee
 * @property Carbon $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['player_id', 'from_squad_id', 'to_squad_id', 'transfer_type', 'transfer_fee', 'transfer_date'])]
class TransferHistory extends Model
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
            'transfer_date' => 'datetime',
            'transfer_fee' => 'integer',
        ];
    }

    /**
     * Get the player that was transferred.
     *
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the origin squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function fromSquad(): BelongsTo
    {
        return $this->belongsTo(Squad::class, 'from_squad_id');
    }

    /**
     * Get the destination squad.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function toSquad(): BelongsTo
    {
        return $this->belongsTo(Squad::class, 'to_squad_id');
    }
}
