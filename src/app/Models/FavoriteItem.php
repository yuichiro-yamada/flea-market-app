<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteItem extends Model
{
    // 紐づく中間テーブル名を明示
    protected $table = 'favorite_items';

    protected $fillable = [
        'user_id',
        'item_id',
    ];

    // お気に入りしたユーザー
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // お気に入りされた商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
