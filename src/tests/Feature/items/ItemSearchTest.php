<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\FavoriteItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    private $ichiro;
    private $otherSeller;
    private $createdItems = [];

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テストユーザーの作成
        $this->ichiro = User::create([
            'member_name' => '一郎',
            'email' => 'ichiro@example.com',
            'password' => bcrypt('11111111'),
        ]);

        $this->otherSeller = User::create([
            'member_name' => '他出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('11111111'),
        ]);

        // 2. 全10商品データの作成
        $itemsData = [
            '腕時計' => ['brand_name' => 'Rolax', 'condition' => 4, 'item_detail' => 'メンズ腕時計', 'item_image' => 'Armani+Mens+Clock.jpg', 'item_price' => 15000, 'sales_status' => 1, 'seller_id' => $this->ichiro->id],
            'HDD' => ['brand_name' => '西芝', 'condition' => 3, 'item_detail' => 'ハードディスク', 'item_image' => 'HDD+Hard+Disk.jpg', 'item_price' => 5000, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            '玉ねぎ3束' => ['brand_name' => 'なし', 'condition' => 2, 'item_detail' => '新鮮な玉ねぎ', 'item_image' => 'iLoveIMG+d.jpg', 'item_price' => 300, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            '革靴' => ['brand_name' => '', 'condition' => 1, 'item_detail' => 'クラシックな革靴', 'item_image' => 'Leather+Shoes+Product+Photo.jpg', 'item_price' => 4000, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            'ノートPC' => ['brand_name' => '', 'condition' => 4, 'item_detail' => 'ノートパソコン', 'item_image' => 'Living+Room+Laptop.jpg', 'item_price' => 45000, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            'マイク' => ['brand_name' => 'なし', 'condition' => 3, 'item_detail' => 'レコーディング用マイク', 'item_image' => 'Music+Mic+4632231.jpg', 'item_price' => 8000, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            'ショルダーバッグ' => ['brand_name' => '', 'condition' => 2, 'item_detail' => 'おしゃれなバッグ', 'item_image' => 'Purse+fashion+pocket.jpg', 'item_price' => 3500, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            'タンブラー' => ['brand_name' => 'なし', 'condition' => 1, 'item_detail' => '使いやすいタンブラー', 'item_image' => 'Tumbler+souvenir.jpg', 'item_price' => 500, 'sales_status' => 1, 'seller_id' => $this->ichiro->id],
            'コーヒーミル' => ['brand_name' => 'Starbacks', 'condition' => 4, 'item_detail' => '手動のコーヒーミル', 'item_image' => 'Waitress+with+Coffee+Grinder.jpg', 'item_price' => 4000, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
            'メイクセット' => ['brand_name' => '', 'condition' => 3, 'item_detail' => 'メイクアップセット', 'item_image' => '%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%8合.jpg', 'item_price' => 2500, 'sales_status' => 1, 'seller_id' => $this->otherSeller->id],
        ];

        foreach ($itemsData as $name => $data) {
            $this->createdItems[$name] = Item::create(array_merge(['item_name' => $name], $data));
        }
    }

    /**
     * テスト1:「商品名」で部分一致検索ができる
     */
    public function test_can_search_items_by_partial_name_match(): void
    {
        $response = $this->actingAs($this->ichiro)->get('/?keyword=PC');

        $response->assertStatus(200);
        $response->assertSee('ノートPC');
        $response->assertDontSee('HDD');
        $response->assertDontSee('玉ねぎ3束');
        $response->assertDontSee('ショルダーバッグ');
    }

    /**
     * テスト2: 検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_is_retained_in_mylist(): void
    {
        // 1. 一郎が「玉ねぎ3束」と「ショルダーバッグ」にいいねをする
        FavoriteItem::create(['user_id' => $this->ichiro->id, 'item_id' => $this->createdItems['玉ねぎ3束']->id]);
        FavoriteItem::create(['user_id' => $this->ichiro->id, 'item_id' => $this->createdItems['ショルダーバッグ']->id]);

        // 2. 一郎でログインし、マイリストページを開く（初期状態はキーワードなし）
        // 「玉ねぎ3束」と「ショルダーバッグ」を表示
        $response = $this->actingAs($this->ichiro)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('玉ねぎ3束');
        $response->assertSee('ショルダーバッグ');

        // 3. 「おすすめ」ページへ行き、「バッグ」で検索を実行
        // 「ショルダーバッグ」のみを表示
        $response = $this->get('/?keyword=バッグ');
        $response->assertStatus(200);
        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('玉ねぎ3束');

        // 4. 実際のブラウザの挙動（リンク遷移時に現在のキーワードを引き継ぐ）に合わせ、keywordを付与してリクエストを送る
        $response = $this->get('/?tab=mylist&keyword=バッグ');
        $response->assertStatus(200);

        // 検索キーワード入力欄（フォーム）に「バッグ」が残っていることを確認
        $response->assertSee('value="バッグ"', false);

        // マイリスト内でも「バッグ」で絞り込まれ、ショルダーバッグだけが表示されていることを確認
        $response->assertSee('ショルダーバッグ');
        $response->assertDontSee('玉ねぎ3束');
    }
}
