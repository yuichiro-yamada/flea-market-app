<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(), // 自動的にユーザーを生成して紐付け
            'item_name' => $this->faker->word() . 'の商品',
            'brand_name' => $this->faker->company(),
            'condition' => $this->faker->numberBetween(1, 4), // 1~4のランダムな状態
            'item_detail' => $this->faker->realText(100),
            'item_image' => 'silver.jpg',
            'item_price' => $this->faker->numberBetween(500, 10000), // 適当な価格
            'sales_status' => 1, // 1: 販売中をデフォルトとする
        ];
    }
}
