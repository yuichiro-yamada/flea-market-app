<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCategory extends Model
{
    // 紐づく中間テーブル名を明示
    protected $table = 'item_categories';

    protected $fillable = [
        'item_id',
        'category_id',
    ];

    // 対象の商品
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // 対象のカテゴリ
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
