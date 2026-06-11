<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存データを一度クリアして重複を防ぐ
        DB::table('item_categories')->truncate();

        // 19個の紐付けデータを定義
        $itemCategories = [
            ['item_id' => 1, 'category_id' => 1],
            ['item_id' => 1, 'category_id' => 5],
            ['item_id' => 1, 'category_id' => 12],
            ['item_id' => 2, 'category_id' => 2],
            ['item_id' => 3, 'category_id' => 10],
            ['item_id' => 4, 'category_id' => 1],
            ['item_id' => 4, 'category_id' => 5],
            ['item_id' => 4, 'category_id' => 11],
            ['item_id' => 5, 'category_id' => 2],
            ['item_id' => 6, 'category_id' => 2],
            ['item_id' => 7, 'category_id' => 1],
            ['item_id' => 7, 'category_id' => 4],
            ['item_id' => 7, 'category_id' => 12],
            ['item_id' => 8, 'category_id' => 5],
            ['item_id' => 8, 'category_id' => 9],
            ['item_id' => 8, 'category_id' => 10],
            ['item_id' => 9, 'category_id' => 10],
            ['item_id' => 10, 'category_id' => 4],
            ['item_id' => 10, 'category_id' => 6],
        ];

        // データベースに一括挿入
        DB::table('item_categories')->insert($itemCategories);
    }
}
