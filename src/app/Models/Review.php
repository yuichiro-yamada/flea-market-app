<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    // 紐づく中間テーブル名を明示
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'item_id',
        'comment',
    ];

    // コメントしたユーザー
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // コメントした商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
