<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesRecord extends Model
{
    protected $fillable = [
        'seller_id',
        'buyer_id',
        'item_id',
        'payment_method',
        'purchase_price',
        'shipping_postcode',
        'shipping_address',
        'shipping_building',
    ];

    // 出品者
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // 購入者
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 購入された商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
