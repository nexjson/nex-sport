<?php

namespace App\Models;

use App\Enums\SquadRequestStatus;
use App\Enums\SquadRequestType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $squad_id
 * @property int $player_id
 * @property SquadRequestType $type
 * @property SquadRequestStatus $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['squad_id', 'player_id', 'type', 'status', 'notes'])]
class SquadRequest extends Model
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
            'type' => SquadRequestType::class,
            'status' => SquadRequestStatus::class,
        ];
    }

    /**
     * Get the squad related to this request.
     *
     * @return BelongsTo<Squad, $this>
     */
    public function squad(): BelongsTo
    {
        return $this->belongsTo(Squad::class);
    }

    /**
     * Get the player related to this request.
     *
     * @return BelongsTo<Player, $this>
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
