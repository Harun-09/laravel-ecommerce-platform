<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentEventLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'provider',
        'payment_method',
        'event_type',
        'status',
        'severity',
        'is_retry',
        'message',
        'context',
        'happened_at',
    ];

    protected $casts = [
        'context' => 'array',
        'is_retry' => 'boolean',
        'happened_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
