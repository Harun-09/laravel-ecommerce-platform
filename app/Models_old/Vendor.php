<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'shop_name',
        'slug',
        'description',
        'logo',
        'banner',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'trade_license',
        'tin_certificate',
        'nid_front',
        'nid_back',
        'bank_name',
        'bank_account_name',
        'bank_account_number',
        'bank_routing_number',
        'mobile_wallet_type',
        'mobile_wallet_number',
        'commission_type',
        'commission_rate',
        'status',
        'rejection_reason',
        'approved_at',
        'rating',
        'total_reviews',
        'total_products',
        'total_orders',
        'total_sales',
        'featured',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'featured' => 'boolean',
        'commission_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_sales' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (empty($vendor->slug)) {
                $vendor->slug = Str::slug($vendor->shop_name);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function payouts()
    {
        return $this->hasMany(VendorPayout::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'vendor_follows')->withTimestamps();
    }

    public function followLinks()
    {
        return $this->hasMany(VendorFollow::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Helpers
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage') . '/' . $this->logo;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->shop_name) . '&background=random&size=200';
    }

    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage') . '/' . $this->banner : null;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getCommissionForProduct(Product $product): float
    {
        // Check category-specific commission first
        if ($product->category && $product->category->commission_rate !== null) {
            return $product->category->commission_rate;
        }
        return $this->commission_rate;
    }

    public function calculateEarning(float $orderTotal): array
    {
        if ($this->commission_type === 'percentage') {
            $commission = ($orderTotal * $this->commission_rate) / 100;
        } else {
            $commission = $this->commission_rate;
        }

        return [
            'commission' => round($commission, 2),
            'earning' => round($orderTotal - $commission, 2),
        ];
    }

    public function getPendingBalance(): float
    {
        $value = $this->orders()
            ->where('status', Order::STATUS_DELIVERED)
            ->whereIn('payment_status', ['paid', 'refunded', 'partially_refunded'])
            ->whereDoesntHave('payoutItems')
            ->selectRaw('COALESCE(SUM(CASE WHEN (total - commission_amount - COALESCE(refunded_amount, 0)) > 0 THEN (total - commission_amount - COALESCE(refunded_amount, 0)) ELSE 0 END), 0) as payable_total')
            ->value('payable_total');

        return (float) $value;
    }

    public function getPendingPayoutLedger(int $limit = 20)
    {
        return $this->orders()
            ->select([
                'id',
                'order_number',
                'total',
                'commission_amount',
                'refunded_amount',
                'payment_status',
                'created_at',
            ])
            ->where('status', Order::STATUS_DELIVERED)
            ->whereIn('payment_status', ['paid', 'refunded', 'partially_refunded'])
            ->whereDoesntHave('payoutItems')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
