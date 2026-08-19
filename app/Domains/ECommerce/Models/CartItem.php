<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variation_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getTotalPriceAttribute(): float
    {
        return round($this->price * $this->quantity, 2);
    }

    public function getProductNameAttribute(): string
    {
        $name = $this->product->name;
        if ($this->variation) {
            $name .= ' - ' . $this->variation->variation_name;
        }
        return $name;
    }

    public function getProductImageAttribute(): string
    {
        if ($this->variation && $this->variation->image) {
            return $this->variation->image_url;
        }
        return $this->product->primary_image_url;
    }

    public function isInStock(): bool
    {
        if ($this->variation) {
            return $this->variation->quantity >= $this->quantity || !$this->product->track_quantity;
        }
        return $this->product->quantity >= $this->quantity || !$this->product->track_quantity;
    }
}

