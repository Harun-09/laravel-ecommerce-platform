<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequestStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'user_id',
        'old_status',
        'new_status',
        'comment',
        'notify_customer',
    ];

    protected $casts = [
        'notify_customer' => 'boolean',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNewStatusLabelAttribute(): string
    {
        return ReturnRequest::statusLabel((string) $this->new_status);
    }

    public function getOldStatusLabelAttribute(): ?string
    {
        if (!$this->old_status) {
            return null;
        }

        return ReturnRequest::statusLabel((string) $this->old_status);
    }
}

