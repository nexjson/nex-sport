<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $min_reward
 * @property int $max_reward
 * @property int $service_fee
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['min_reward', 'max_reward', 'service_fee'])]
class ServiceFeeConfig extends Model
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
            'min_reward' => 'integer',
            'max_reward' => 'integer',
            'service_fee' => 'integer',
        ];
    }
}
