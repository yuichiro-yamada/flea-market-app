<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'id' => 1,
                'seller_id' => 1,
                'item_name' => '腕時計',
                'brand_name' => 'Rolax',
                'condition' => 4,
                'item_detail' => 'スタイリッシュなデザイン of メンズ腕時計',
                'item_image' => 'Armani+Mens+Clock.jpg',
                'item_price' => 15000,
                'sales_status' => 1,
            ],
            [
                'id' => 2,
                'seller_id' => 2,
                'item_name' => 'HDD',
                'brand_name' => '西芝',
                'condition' => 3,
                'item_detail' => '高速で信頼性の高いハードディスク',
                'item_image' => 'HDD+Hard+Disk.jpg',
                'item_price' => 5000,
                'sales_status' => 1,
            ],
            [
                'id' => 3,
                'seller_id' => 3,
                'item_name' => '玉ねぎ3束',
                'brand_name' => null,
                'condition' => 2,
                'item_detail' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'iLoveIMG+d.jpg',
                'item_price' => 300,
                'sales_status' => 1,
            ],
            [
                'id' => 4,
                'seller_id' => 4,
                'item_name' => '革靴',
                'brand_name' => null,
                'condition' => 1,
                'item_detail' => 'クラシックなデザインの革靴',
                'item_image' => 'Leather+Shoes+Product+Photo.jpg',
                'item_price' => 4000,
                'sales_status' => 1,
            ],
            [
                'id' => 5,
                'seller_id' => 4,
                'item_name' => 'ノートPC',
                'brand_name' => null,
                'condition' => 4,
                'item_detail' => '高性能なノートパソコン',
                'item_image' => 'Living+Room+Laptop.jpg',
                'item_price' => 45000,
                'sales_status' => 1,
            ],
            [
                'id' => 6,
                'seller_id' => 3,
                'item_name' => 'マイク',
                'brand_name' => null,
                'condition' => 3,
                'item_detail' => '高音質のレコーディング用マイク',
                'item_image' => 'Music+Mic+4632231.jpg',
                'item_price' => 8000,
                'sales_status' => 1,
            ],
            [
                'id' => 7,
                'seller_id' => 2,
                'item_name' => 'ショルダーバッグ',
                'brand_name' => null,
                'condition' => 2,
                'item_detail' => 'おしゃれなショルダーバッグ',
                'item_image' => 'Purse+fashion+pocket.jpg',
                'item_price' => 3500,
                'sales_status' => 1,
            ],
            [
                'id' => 8,
                'seller_id' => 1,
                'item_name' => 'タンブラー',
                'brand_name' => null,
                'condition' => 1,
                'item_detail' => '使いやすいタンブラー',
                'item_image' => 'Tumbler+souvenir.jpg',
                'item_price' => 500,
                'sales_status' => 1,
            ],
            [
                'id' => 9,
                'seller_id' => 3,
                'item_name' => 'コーヒーミル',
                'brand_name' => 'Starbacks',
                'condition' => 4,
                'item_detail' => '手動のコーヒーミル',
                'item_image' => 'Waitress+with+Coffee+Grinder.jpg',
                'item_price' => 4000,
                'sales_status' => 1,
            ],
            [
                'id' => 10,
                'seller_id' => 2,
                'item_name' => 'メイクセット',
                'brand_name' => null,
                'condition' => 3,
                'item_detail' => '便利なメイクアップセット',
                'item_image' => '%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'item_price' => 2500,
                'sales_status' => 1,
            ],
        ]);
    }
}
