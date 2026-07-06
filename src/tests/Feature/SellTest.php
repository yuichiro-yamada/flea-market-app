<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. 商品出品画面にて必要な情報が保存できること
     */
    public function test_1_品出品画面にて必要な情報が保存できること(): void
    {
        // 仮想ストレージの準備
        Storage::fake('public');

        // 1. 出品を行うログインユーザーを作成
        $user = User::factory()->create();

        // カテゴリマスターデータをDBにあらかじめ作成（ID: 1 と 2）
        $category1_id = \DB::table('categories')->insertGetId(['category_name' => 'ファッション']);
        $category2_id = \DB::table('categories')->insertGetId(['category_name' => 'メンズ']);

        // 2. 出品画面を開く（GET /sell）
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);

        // 3. テスト用のダミー画像ファイルを作成（GD拡張機能がなくても動く安全な形式）
        $dummy_image = UploadedFile::fake()->create('test_product.jpg', 100, 'image/jpeg');

        // 4. 各項目に入力するテストデータを用意
        // 💡 分離設計に合わせて、不要になった 'action' などの隠しパラメータを綺麗に削除しました。
        // 💡 キー名もすべて「condition」に一本化されています。
        $item_data = [
            'category_ids' => [$category1_id, $category2_id],
            'condition'    => 2,
            'item_name'    => 'テスト出品の商品名',
            'brand_name'   => 'テストブランド',
            'item_detail'  => 'これはテスト出品された商品の詳しい説明文です。',
            'item_price'   => 3500,
            'item_image'   => $dummy_image,
        ];

        // 商品保存リクエストを送信 (POST /sell)
        $response = $this->post('/sell', $item_data);

        // 5. 保存成功後、仕様通りマイページ（/mypage）へリダイレクトされるか検証
        $response->assertRedirect('/mypage');

        // 6. items テーブルに適切な値が正しく保存されているか検証
        $this->assertDatabaseHas('items', [
            'seller_id'   => $user->id,
            'item_name'   => 'テスト出品の商品名',
            'brand_name'  => 'テストブランド',
            'item_detail' => 'これはテスト出品された商品の詳しい説明文です。',
            'item_price'  => 3500,
            'condition'   => 2,
        ]);

        // 保存された商品のIDを特定
        $saved_item = Item::where('item_name', 'テスト出品の商品名')->first();

        // 7. 中間テーブル（item_categories）に複数のカテゴリが紐付いて保存されているか検証
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $saved_item->id,
            'category_id' => $category1_id,
        ]);

        $this->assertDatabaseHas('item_categories', [
            'item_id' => $saved_item->id,
            'category_id' => $category2_id,
        ]);
    }
}
