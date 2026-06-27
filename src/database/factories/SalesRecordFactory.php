<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;
use App\Models\SalesRecord;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesRecord>
 */
class SalesRecordFactory extends Factory
{
    protected $model = SalesRecord::class;

    public function definition(): array
    {
        return [
            'seller_id' => User::factory(),
            'buyer_id' => User::factory(),
            'item_id' => Item::factory(),
            'payment_method' => $this->faker->numberBetween(0, 1), // 0か1
            'purchase_price' => $this->faker->numberBetween(500, 10000),
            'shipping_postcode' => '1234567',
            'shipping_address' => '東京都港区芝公園4-2-8',
            'shipping_building' => '東京タワービル',
        ];
    }
}
