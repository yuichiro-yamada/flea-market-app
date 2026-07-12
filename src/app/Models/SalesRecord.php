<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Item;

class SalesRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'item_id',
        'payment_method',
        'purchase_price',
        'shipping_postcode',
        'shipping_address',
        'shipping_building',
        'purchase_status'
    ];

    // 出品者とusersテーブル（多対１）
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // 購入者とusersテーブル（多対１）
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // 購入された商品
    public function item(): HasOne
    {
        return $this->hasOne(Item::class);
    }
}
