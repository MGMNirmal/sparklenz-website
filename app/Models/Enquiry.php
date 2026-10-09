<?php

namespace App\Models;

use App\Enums\EnquiryStatus;
use Database\Factories\EnquiryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'email', 'phone', 'service_interest', 'message', 'status', 'ip_hash',
    'user_agent', 'notified_at', 'read_at', 'replied_at',
])]
class Enquiry extends Model
{
    /** @use HasFactory<EnquiryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => EnquiryStatus::class,
            'notified_at' => 'datetime',
            'read_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }
}
