<?php

namespace App\Models;

use App\Enums\GameCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property GameCategory $category
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'category', 'status'])]
class Game extends Model
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
            'category' => GameCategory::class,
            'status' => 'boolean',
        ];
    }

    /**
     * Get the squads that play this game.
     *
     * @return HasMany<Squad, $this>
     */
    public function squads(): HasMany
    {
        return $this->hasMany(Squad::class);
    }

    /**
     * Get the roles available in this game.
     *
     * @return HasMany<GameRole, $this>
     */
    public function gameRoles(): HasMany
    {
        return $this->hasMany(GameRole::class);
    }

    /**
     * Get the event games mapping for this game.
     *
     * @return HasMany<EventGame, $this>
     */
    public function eventGames(): HasMany
    {
        return $this->hasMany(EventGame::class);
    }
}
