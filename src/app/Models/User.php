<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_name',
        'email',
        'password',
        'member_image',
        'postcode',
        'address',
        'building',
        'is_withdrawn',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 出品した商品
    public function soldItems(): HasMany
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    // お気に入りした商品（多対多）
    public function favoriteItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'favorite_items', 'user_id', 'item_id')
                    ->withTimestamps();
    }

    // 投稿したコメント一覧
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // 販売履歴（出品者側）
    public function salesRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class, 'seller_id');
    }

    // 購入履歴（購入者側）
    public function purchaseRecords(): HasMany
    {
        return $this->hasMany(SalesRecord::class, 'buyer_id');
    }
}
