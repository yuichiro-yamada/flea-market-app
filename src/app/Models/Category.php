<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'category_name',
    ];

    // カテゴリとitem_categoriesテーブルとitemsテーブル（多対多）
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_categories', 'category_id', 'item_id')
                    ->withTimestamps();
    }
}
