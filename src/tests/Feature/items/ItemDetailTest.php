<?php

namespace Tests\Feature\Items;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    private $ichiro;
    private $jiro;
    private $otherSeller;
    private $item;
    private $category1;
    private $category2;
    private $commentText = 'いつごろ購入されたものですか？';

    /**
     * テスト実行前の共通データ準備
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. テストユーザーの作成
        $this->ichiro = User::create([
            'member_name' => '一郎',
            'email' => 'ichiro@example.com',
            'password' => bcrypt('11111111'),
            'member_image' => 'beer.jpg', // パス検証用に画像名を設定
        ]);

        $this->jiro = User::create([
            'member_name' => '二郎',
            'email' => 'jiro@example.com',
            'password' => bcrypt('11111111'),
            'member_image' => 'tomato.jpg',
        ]);

        // 2. カテゴリの作成
        $this->category1 = Category::create(['category_name' => 'メンズ']);
        $this->category2 = Category::create(['category_name' => 'アクセサリー']);

        // 3. 二郎（jiro）を出品者として「腕時計」商品を登録
        $this->item = Item::create([
            'seller_id' => $this->jiro->id,
            'item_name' => '腕時計',
            'brand_name' => 'Rolax',
            'condition' => 4, // 良好
            'item_detail' => 'スタイリッシュなデザインのメンズ腕時計',
            'item_image' => 'Armani+Mens+Clock.jpg',
            'item_price' => 15000,
            'sales_status' => 1,
        ]);

        // 4. 商品に複数カテゴリを紐付け
        $this->item->categories()->attach([$this->category1->id, $this->category2->id]);

        // 5. 一郎が「いいね」とお気に入り登録（DBファサードで直接挿入）
        \Illuminate\Support\Facades\DB::table('favorite_items')->insert([
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. 一郎がコメントを投稿（DBファサードで直接挿入）
        \Illuminate\Support\Facades\DB::table('reviews')->insert([
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
            'comment' => $this->commentText,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * テスト1：すべての情報が商品詳細ページに表示されている
     */
    public function test_1_all_information_is_displayed_on_the_item_detail_page()
    {
        // 一郎としてログインして商品詳細画面にアクセス
        $response = $this->actingAs($this->ichiro)
            ->get("/item/{$this->item->id}");

        $response->assertStatus(200);

        // 1. 商品の基本情報
        $response->assertSee($this->item->item_name);        // 商品名: 腕時計
        $response->assertSee($this->item->brand_name);       // ブランド名: Rolax
        $response->assertSee(number_format($this->item->item_price)); // 価格: 15,000
        $response->assertSee($this->item->item_detail);      // 商品説明

        // 2. 正しいパスを含んだ商品画像
        $expectedItemImagePath = '/storage/images/items/' . $this->item->item_image;
        $response->assertSee($expectedItemImagePath);

        // 3. 商品の状態（4 = 良好）
        $response->assertSee('良好');

        // 4. カウント情報（いいね数・コメント数）
        $response->assertSee('1');

        // 5. コメントしたユーザーの情報（一郎）とコメント内容
        $response->assertSee($this->ichiro->member_name);   // ユーザー名: 一郎
        $this->assertSeeProfileImage($response);            // プロフィール画像のパス
        $response->assertSee($this->commentText);           // コメント: いつごろ購入されたものですか？
    }

    /**
     * テスト2：複数選択されたカテゴリが商品詳細ページに表示されている
     */
    public function test_2_multiple_selected_categories_are_displayed_on_the_item_detail_page()
    {
        // 一郎としてログインして商品詳細画面にアクセス
        $response = $this->actingAs($this->ichiro)
            ->get("/item/{$this->item->id}");

        $response->assertStatus(200);

        // 紐付けた複数のカテゴリ名が両方とも表示されているか検証
        $response->assertSee($this->category1->category_name); // メンズ
        $response->assertSee($this->category2->category_name); // アクセサリー
    }

    /**
     * プロフィール画像パスの検証用補助メソッド
     */
    private function assertSeeProfileImage($response)
    {
        $expectedProfileImagePath = '/storage/images/profile/' . $this->ichiro->member_image;
        $response->assertSee($expectedProfileImagePath);
    }
}
