<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Item extends Model
{
    use HasFactory;

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


    /* conditionの値を数字として定義 */
    protected $casts = [
        'condition' => 'integer',
    ];

    /* 商品の状態を数値から文字列に変換するアクセサ */
    public function getConditionTextAttribute()
    {
        // $this->condition と書くことで、上記の $casts が適用された綺麗な数値が取れます
        $value = $this->condition ?? null;

        return match ($value) {
            1 => '状態が悪い',
            2 => 'やや傷や汚れあり',
            3 => '目立った傷や汚れなし',
            4 => '良好',
            default => '不明',
        };
    }

    /**
     * 商品の価格を「¥3,000」の形式に変換するアクセサ
     * メソッド名を「get + カメルケースのカラム名 + Formatted + Attribute」にしています
     */
    public function getItemPriceFormattedAttribute()
    {
        // データベースから生の価格（数値）を取得します
        $price = $this->attributes['item_price'] ?? 0;

        // number_format でカンマ区切りにし、先頭に「¥」をくっつけて返します
        return number_format($price);
    }


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
