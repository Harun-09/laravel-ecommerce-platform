<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'pros',
        'cons',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'admin_reply',
        'admin_replied_at',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'admin_replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class)->orderBy('order');
    }

    public function helpfuls()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    public function approve(): void
    {
        $this->update(['is_approved' => true]);
        $this->product->updateRating();
    }

    public function reject(): void
    {
        $this->update(['is_approved' => false]);
        $this->product->updateRating();
    }

    public function addHelpful(User $user, bool $isHelpful = true): void
    {
        $this->helpfuls()->updateOrCreate(
            ['user_id' => $user->id],
            ['is_helpful' => $isHelpful]
        );

        $this->helpful_count = $this->helpfuls()->where('is_helpful', true)->count();
        $this->save();
    }

    public function addReply(string $reply): void
    {
        $this->update([
            'admin_reply' => $reply,
            'admin_replied_at' => now(),
        ]);
    }
}
