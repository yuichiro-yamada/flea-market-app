<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 14個のカテゴリ名だけを定義
        $categories = [
            ['id' => 1, 'category_name' => 'ファッション'],
            ['id' => 2, 'category_name' => '家電'],
            ['id' => 3, 'category_name' => 'インテリア'],
            ['id' => 4, 'category_name' => 'レディース'],
            ['id' => 5, 'category_name' => 'メンズ'],
            ['id' => 6, 'category_name' => 'コスメ'],
            ['id' => 7, 'category_name' => '本'],
            ['id' => 8, 'category_name' => 'ゲーム'],
            ['id' => 9, 'category_name' => 'スポーツ'],
            ['id' => 10, 'category_name' => 'キッチン'],
            ['id' => 11, 'category_name' => 'ハンドメイド'],
            ['id' => 12, 'category_name' => 'アクセサリー'],
            ['id' => 13, 'category_name' => 'おもちゃ'],
            ['id' => 14, 'category_name' => 'ベビー・キッズ'],
        ];

        // データベースに一括挿入
        DB::table('categories')->insert($categories);
    }
}
