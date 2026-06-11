<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    protected $fillable = [
        'seller_id',
        'item_name',
        'brand_name',
        'condition',
        'item_detail',
        'item_image',
        'item_price',
        'sales_status',
    ];

    // 出品者
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // この商品をお気に入りしているユーザー（多対多）
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_items', 'item_id', 'user_id')
                    ->withTimestamps();
    }

    // 商品へのコメント一覧
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // 紐づくカテゴリ（多対多）
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'item_categories', 'item_id', 'category_id')
                    ->withTimestamps();
    }

    // 販売履歴（1対1の関係）
    public function salesRecord(): HasOne
    {
        return $this->hasOne(SalesRecord::class);
    }
}
