<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\SalesRecord;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    private $buyer;
    private $seller;
    private $item;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. テスト用ユーザー（購入者）の初期化（DB構造に準拠）
        $this->buyer = User::factory()->create([
            'default_payment_method' => 1, // 1: コンビニ払い
            'postcode' => '1111111',       // ハイフンなし7桁想定
            'address' => '東京都渋谷区',
            'building' => 'テックビル101',
            'shipping_postcode' => null,   // 初期状態は未登録
            'shipping_address' => null,
            'shipping_building' => null,
        ]);

        // 販売者の初期化
        $this->seller = User::factory()->create();

        // 2. テスト用商品の初期化（DB構造に準拠）
        $this->item = Item::factory()->create([
            'seller_id' => $this->seller->id,
            'item_name' => 'テスト商品A',
            'item_price' => 5000,
            'sales_status' => 1, // 1: 販売中
        ]);
    }

    /**
     * テストNo.1-1: クレジットカート決済で「購入する」ボタンを押下すると支払い済みで購入が完了する
     */
    public function  test_1_1_クレジットカード決済で「購入する」ボタンを押下すると支払い済みで購入が完了する(): void
    {
        // ユーザーにログインして、商品購入画面を開く
        $response = $this->actingAs($this->buyer)->get("/purchase/{$this->item->id}");
        $response->assertStatus(200);

        // 商品を選択して支払い方法をクレジットカード決済にして「購入する」ボタンを押下
        $response = $this->post("/checkout", [
            'item_id'        => $this->item->id,
            'payment_method' => 0,  // クレジットカード決済
        ]);

        // 購入完了後のリダイレクト先を検証
        $response->assertRedirect('/mypage?page=buy');

        // sales_recordsテーブルのpurchase_status（購買状態）が３（支払い済み）になっているか検証
        $this->assertDatabaseHas('sales_records', [
            'seller_id' => $this->seller->id,
            'buyer_id' => $this->buyer->id,
            'item_id' => $this->item->id,
            'payment_method' => 0,  // クレジットカード決済
            'purchase_price' => $this->item->item_price,
            'shipping_postcode' => $this->buyer->postcode,
            'shipping_address' => $this->buyer->address,
            'shipping_building' => $this->buyer->building,
            'purchase_status' => 3, // 支払い済み
        ]);

        // itemsのテーブルsales_status(販売状態)が３（SOLD　OUT）になっているか検証
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'sales_status' => 3, // SOLD OUT
        ]);
    }

    /**
     * テストNo.1−２: コンビニ決済で「購入する」ボタンを押下すると支払い待ちで購入が完了する
     */
    public function  test_1_2_コンビニ決済で「購入する」ボタンを押下すると支払い待ちで購入が完了する(): void
    {
        // ユーザーにログインして、商品購入画面を開く
        $response = $this->actingAs($this->buyer)->get("/purchase/{$this->item->id}");
        $response->assertStatus(200);

        // 商品を選択して支払い方法をコンビニ決済にして「購入する」ボタンを押下
        $response = $this->post("/checkout", [
            'item_id'        => $this->item->id,
            'payment_method' => 1,  // コンビニ決済
        ]);

        // 購入完了後のリダイレクト先を検証
        $response->assertRedirect('/mypage?page=buy');

        // sales_recordsテーブルのpurchase_status（購買状態）が2（支払い待ち）になっているか検証
        $this->assertDatabaseHas('sales_records', [
            'seller_id' => $this->seller->id,
            'buyer_id' => $this->buyer->id,
            'item_id' => $this->item->id,
            'payment_method' => 1,  // コンビニ決済
            'purchase_price' => $this->item->item_price,
            'shipping_postcode' => $this->buyer->postcode,
            'shipping_address' => $this->buyer->address,
            'shipping_building' => $this->buyer->building,
            'purchase_status' => 2, // 支払い待ち
        ]);

        // itemsのテーブルsales_status(販売状態)が2（取引中）になっているか検証
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'sales_status' => 2, // 取引中
        ]);
    }

    /**
     * テストNo.2: 購入した商品は商品一覧画面にて「SOLD」と表示される
     */
    public function test_2_購入した商品は商品一覧画面にて「SOLD」と表示される(): void
    {
        // 商品の販売状態を「3: SOLD OUT」に変更
        $this->item->update(['sales_status' => 3]);

        // 4. 商品一覧画面を表示する（ルートは仮に '/' としています）
        $response = $this->get('/');
        $response->assertStatus(200);

        // 商品画像の上に「SOLD」の文字が表示されているか確認
        $response->assertSee('SOLD');
    }

    /**
     * テストNo.3: 「プロフィール/購入した商品一覧」に追加されている
     */
    public function test_3_「プロフィール／購入した商品一覧」に追加されている(): void
    {
        // 1. 既存の商品の buyer_id を購入者のIDに更新する
        $this->item->update([
            'buyer_id' => $this->buyer->id
        ]);
        // ２。 購入レコードを作成
        SalesRecord::factory()->create([
            'seller_id' => $this->seller->id,
            'buyer_id' => $this->buyer->id,
            'item_id' => $this->item->id,
            'payment_method' => $this->buyer->default_payment_method,
            'purchase_price' => $this->item->item_price,
            'shipping_postcode' => $this->buyer->postcode,
            'shipping_address' => $this->buyer->address,
            'shipping_building' => $this->buyer->building,
        ]);

        // 3. プロフィール画面を表示する
        $response = $this->actingAs($this->buyer)->get('/mypage?page=buy');
        $response->assertStatus(200);

        // 4. 購入した商品の「商品名」が画面に含まれているか確認
        $response->assertSee($this->item->item_name);
    }

    /**
     * テストNo.4: 小計画面で変更が反映される

     */
    public function test_4_小計画面で変更が反映される(): void
    {
        // 1. 支払い方法選択画面（購入画面）を開く
        $this->actingAs($this->buyer);

        // 2. プルダウンメニューから支払い方法を選択する
        // 変更があった場合はすぐにDBに書き込む仕様に基づき、更新リクエストを送信
        $new_payment_method = 0; // 0: カード支払い に変更
        
        $response = $this->patch("/purchase/{$this->item->id}", [
            'payment_method' => $new_payment_method,
        ]);

        // users テーブルの default_payment_method が即時書き込まれているか検証
        $this->assertDatabaseHas('users', [
            'id' => $this->buyer->id,
            'default_payment_method' => $new_payment_method,
        ]);

        // 選択した支払い方法が正しく反映（表示）されているか検証
        $response = $this->get("/purchase/{$this->item->id}");
        $response->assertSee($new_payment_method);
    }

    /**
     * テストNo.5: 送付先住所変更画面にて登録した住所が商品購入画面に反映されている
     */
    public function test_5_付先住所変更画面にて登録した住所が商品購入画面に反映されている(): void
    {
        $this->actingAs($this->buyer);

        // 2. 送付先住所変更画面で住所を登録する
        $shipping_data = [
            'shipping_postcode' => '2222222',
            'shipping_address' => '神奈川県横浜市',
            'shipping_building' => 'みなとビル202',
        ];
        
        // 住所更新用URLへのリクエスト（処理内でusersテーブルのshipping_*を更新する想定）
        $this->post("/purchase/address/{$this->item->id}", $shipping_data);

        // 3. 商品購入画面を再度開く
        $response = $this->get("/purchase/{$this->item->id}");
        
        // お届け先住所（shipping_*）の情報が表示されているか検証
        $response->assertSee($shipping_data['shipping_postcode']);
        $response->assertSee($shipping_data['shipping_address']);
        $response->assertSee($shipping_data['shipping_building']);
    }

    /**
     * テストNo.6: 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_6_購入した商品に送付先住所が紐づいて登録される(): void
    {
        $this->actingAs($this->buyer);

        // 2. 送付先住所変更画面で住所を登録する（事前にユーザー情報のお届け先を更新）
        $this->buyer->update([
            'shipping_postcode' => '3333333',
            'shipping_address' => '埼玉県さいたま市',
            'shipping_building' => 'さいたまタワー303',
        ]);

        // 3. 商品を購入する
        $this->post('/checkout', [
            'item_id' => $this->item->id,
            'payment_method' => $this->buyer->default_payment_method,
        ]);

        // sales_records に通常の住所ではなく、お届け先住所（shipping_*）が紐づいて保存されているか検証
        $this->assertDatabaseHas('sales_records', [
            'item_id' => $this->item->id,
            'buyer_id' => $this->buyer->id,
            'shipping_postcode' => '3333333',
            'shipping_address' => '埼玉県さいたま市',
            'shipping_building' => 'さいたまタワー303',
        ]);
    }
}
