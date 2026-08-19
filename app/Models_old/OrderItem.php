<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variation_id',
        'product_name',
        'product_sku',
        'variation_details',
        'product_image',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'variation_details' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getProductImageUrlAttribute(): string
    {
        if ($this->product_image) {
            return asset('storage') . '/' . $this->product_image;
        }
        return asset('images') . '/no-product-image.png';
    }
}
