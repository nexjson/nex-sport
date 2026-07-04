<?php

namespace App\Enums;

enum RewardClaimStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Paid = 'paid';
    case Failed = 'failed';
}
