<?php

namespace App\Enums;

enum EventPaymentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Refunded = 'refunded';
}
