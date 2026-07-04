<?php

namespace App\Models;

use App\Enums\EventPaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $event_id
 * @property int $amount
 * @property int $service_fee
 * @property string|null $payment_receipt
 * @property string|null $voucher_code
 * @property EventPaymentStatus $status
 * @property int|null $verified_by_id
 * @property Carbon|null $verified_at
 * @property Carbon|null $refunded_at
 * @property string|null $refund_receipt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'event_id', 'amount', 'service_fee', 'payment_receipt',
    'voucher_code', 'status', 'verified_by_id', 'verified_at',
    'refunded_at', 'refund_receipt',
])]
class EventPayment extends Model
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
            'status' => EventPaymentStatus::class,
            'amount' => 'integer',
            'service_fee' => 'integer',
            'verified_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Get the event associated with this payment.
     *
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the admin user who verified this payment.
     *
     * @return BelongsTo<User, $this>
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }
}
