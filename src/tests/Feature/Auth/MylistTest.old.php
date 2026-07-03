<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\FavoriteItem;
use App\Models\SalesRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    private $ichiro;
    private $jiro;
    private $otherSeller;
    private $allItemNames = [];

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テストユーザーの作成
        $this->ichiro = User::create([
            'member_name' => '一郎',
            'email' => 'ichiro@example.com',
            'password' => bcrypt('11111111'),
        ]);

        $this->jiro = User::create([
            'member_name' => '二郎',
            'email' => 'jiro@example.com',
            'password' => bcrypt('11111111'),
        ]);

        $this->otherSeller = User::create([
            'member_name' => '他出品者',
            'email' => 'seller@example.com',
            'password' => bcrypt('11111111'),
        ]);

        // 2. 全商品データの作成
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

        $createdItems = [];
        foreach ($itemsData as $name => $data) {
            $createdItems[$name] = Item::create(array_merge(['item_name' => $name], $data));
            $this->allItemNames[] = $name;
        }

        // 3. 一郎の購入履歴を作成（ノートPC）
        $createdItems['ノートPC']->update(['sales_status' => 3]);
        
        SalesRecord::forceCreate([
            'seller_id' => $this->otherSeller->id,
            'buyer_id' => $this->ichiro->id,
            'item_id' => $createdItems['ノートPC']->id,
            'payment_method' => 0,
            'purchase_price' => 45000, // 修正：purchase_priceに統一
            'shipping_postcode' => '0000000',
            'shipping_address' => 'テスト住所',
        ]);

        // 4. 一郎がいいねをした商品の紐付け
        FavoriteItem::create(['user_id' => $this->ichiro->id, 'item_id' => $createdItems['革靴']->id]);
        FavoriteItem::create(['user_id' => $this->ichiro->id, 'item_id' => $createdItems['タンブラー']->id]);
        FavoriteItem::create(['user_id' => $this->ichiro->id, 'item_id' => $createdItems['コーヒーミル']->id]);
    }

    /**
     * テスト1: いいねした商品だけが表示される
     */
    public function test_only_favorited_items_are_displayed(): void
    {
        $response = $this->actingAs($this->ichiro)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('革靴');
        $response->assertSee('コーヒーミル');
        $response->assertDontSee('HDD');
        $response->assertDontSee('マイク');
        $response->assertDontSee('タンブラー');
    }

    /**
     * テスト2: 購入済み商品は「Sold」と表示される
     */
    public function test_purchased_items_display_sold_label(): void
    {
        $tumblr = Item::where('item_name', 'タンブラー')->first();

        FavoriteItem::create(['user_id' => $this->jiro->id, 'item_id' => $tumblr->id]);

        $tumblr->update(['sales_status' => 3]);
        
        SalesRecord::forceCreate([
            'seller_id' => $this->ichiro->id,
            'buyer_id' => $this->jiro->id,
            'item_id' => $tumblr->id,
            'payment_method' => 0,
            'purchase_price' => 500, // 修正：purchase_priceに統一
            'shipping_postcode' => '0000000',
            'shipping_address' => 'テスト住所',
        ]);

        $response = $this->actingAs($this->jiro)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('タンブラー');
        $response->assertSee('SOLD'); 
    }

    /**
     * テスト3: 未認証の場合は何も表示されない
     */
    public function test_unauthenticated_user_sees_nothing(): void
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        foreach ($this->allItemNames as $name) {
            $response->assertDontSee($name);
        }
    }
}
