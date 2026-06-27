<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
// メモリ上に一時的なDBとテーブルを作る設定に変更
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemListTest extends TestCase
{
    // テスト実行時に自動でマイグレーションを実行し、終了後にデータを消去します
    use RefreshDatabase;

    private $loginUser;
    private $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テストユーザー（一郎）をメモリ上に作成
        $this->loginUser = User::create([
            'id' => 1,
            'member_name' => '一郎',
            'email' => 'ichiro@example.com',
            'password' => bcrypt('11111111'),
        ]);

        // 2. 他の出品者（二郎）をメモリ上に作成
        $this->otherUser = User::create([
            'id' => 2,
            'member_name' => '二郎',
            'email' => 'jiro@example.com',
            'password' => bcrypt('11111111'),
        ]);

        // 3. 他人が出品した商品の作成（一覧に表示されるべき商品）
        $items = [
            ['item_name' => 'HDD', 'brand_name' => '西芝', 'condition' => 3, 'item_detail' => 'ハードディスク', 'item_image' => 'HDD.jpg', 'item_price' => 5000, 'sales_status' => 1],
            ['item_name' => '玉ねぎ3束', 'brand_name' => 'なし', 'condition' => 2, 'item_detail' => '玉ねぎ', 'item_image' => 'onion.jpg', 'item_price' => 300, 'sales_status' => 1],
            ['item_name' => '革靴', 'brand_name' => '', 'condition' => 1, 'item_detail' => '革靴', 'item_image' => 'shoes.jpg', 'item_price' => 4000, 'sales_status' => 1],
            ['item_name' => 'ノートPC', 'brand_name' => '', 'condition' => 4, 'item_detail' => 'パソコン', 'item_image' => 'pc.jpg', 'item_price' => 45000, 'sales_status' => 1],
            ['item_name' => 'マイク', 'brand_name' => 'なし', 'condition' => 3, 'item_detail' => 'マイク', 'item_image' => 'mic.jpg', 'item_price' => 8000, 'sales_status' => 1],
            ['item_name' => 'ショルダーバッグ', 'brand_name' => '', 'condition' => 2, 'item_detail' => 'バッグ', 'item_image' => 'bag.jpg', 'item_price' => 3500, 'sales_status' => 1],
            ['item_name' => 'コーヒーミル', 'brand_name' => 'Starbacks', 'condition' => 4, 'item_detail' => 'ミル', 'item_image' => 'mill.jpg', 'item_price' => 4000, 'sales_status' => 1],
            ['item_name' => 'メイクセット', 'brand_name' => '', 'condition' => 3, 'item_detail' => 'メイク', 'item_image' => 'make.jpg', 'item_price' => 2500, 'sales_status' => 1],
        ];

        foreach ($items as $item) {
            Item::create(array_merge($item, ['seller_id' => $this->otherUser->id]));
        }

        // 4. 一郎（自分）が出品した商品の作成（一覧に表示されないべき商品）
        Item::create(['seller_id' => $this->loginUser->id, 'item_name' => '腕時計', 'condition' => 4, 'item_detail' => '時計', 'item_image' => 'watch.jpg', 'item_price' => 15000, 'sales_status' => 1]);
        Item::create(['seller_id' => $this->loginUser->id, 'item_name' => 'タンブラー', 'condition' => 1, 'item_detail' => 'タンブラー', 'item_image' => 'tumbler.jpg', 'item_price' => 500, 'sales_status' => 1]);
    }

    /**
     * テスト1: 全商品を取得できる
     */
    public function test_can_get_all_products(): void
    {
        $response = $this->actingAs($this->loginUser)->get('/');

        $response->assertStatus(200);
        $response->assertSee('HDD');
        $response->assertSee('玉ねぎ3束');
        $response->assertSee('革靴');
        $response->assertSee('ノートPC');
        $response->assertSee('マイク');
        $response->assertSee('ショルダーバッグ');
        $response->assertSee('コーヒーミル');
        $response->assertSee('メイクセット');
    }

    /**
     * テスト2: 購入された商品は「SOLD」と表示される
     */
    public function test_purchased_products_display_sold_label(): void
    {
        // メモリ上のノートPCをSOLD OUT状態（sales_status = 3）に更新
        $purchasedItem = Item::where('item_name', 'ノートPC')->first();
        if ($purchasedItem) {
            $purchasedItem->update(['sales_status' => 3]);
        }

        $response = $this->actingAs($this->loginUser)->get('/');

        $response->assertStatus(200);
        $response->assertSee('ノートPC');
        $response->assertSee('SOLD'); 
    }

    /**
     * テスト3: 一郎（自分）が出品した商品は表示されない
     */
    public function test_my_products_are_not_displayed(): void
    {
        $response = $this->actingAs($this->loginUser)->get('/');

        $response->assertStatus(200);
        $response->assertSee('HDD'); 
        
        // 自分が出品した商品は表示されないことを検証
        $response->assertDontSee('腕時計');   
        $response->assertDontSee('タンブラー'); 
    }
}
