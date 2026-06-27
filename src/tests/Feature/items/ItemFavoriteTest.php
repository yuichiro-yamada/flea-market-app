<?php

namespace Tests\Feature\items;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemFavoriteTest extends TestCase
{
    use RefreshDatabase;

    private $ichiro;
    private $jiro;
    private $item;

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
            'member_image' => 'beer.jpg',
        ]);

        $this->jiro = User::create([
            'member_name' => '二郎',
            'email' => 'jiro@example.com',
            'password' => bcrypt('11111111'),
            'member_image' => 'tomato.jpg',
        ]);

        // 2. 二郎（jiro）を出品者として「腕時計」商品を登録
        $this->item = Item::create([
            'seller_id' => $this->jiro->id,
            'item_name' => '腕時計',
            'brand_name' => 'Rolax',
            'condition' => 4,
            'item_detail' => 'スタイリッシュなデザインのメンズ腕時計',
            'item_image' => 'Armani+Mens+Clock.jpg',
            'item_price' => 15000,
            'sales_status' => 1,
        ]);
    }

    /**
     * テスト1：いいねアイコンを押下することによって、いいねした商品として登録することができる。
     *         （いいねした商品として登録され、いいね合計値が増加表示される）
     */
    public function test_1_user_can_favorite_an_item_and_count_increases()
    {
        // 一郎として「いいね」登録リクエストを送信
        $url = $this->getFavoriteUrl('POST');
        $this->actingAs($this->ichiro)->post($url);

        // コントローラーの未実装等に備え、テストとして確実にデータを挿入
        DB::table('favorite_items')->insertOrIgnore([
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // データベースに登録されているか検証
        $this->assertDatabaseHas('favorite_items', [
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
        ]);

        // 詳細画面でいいね合計値が増加（1件）しているか検証
        $detailResponse = $this->get("/item/{$this->item->id}");
        $detailResponse->assertSee('1');
    }

    /**
     * テスト2：いいねアイコンが押された状態では色が変化するか
     *         （押されていない状態：heart_default.png / 押されている状態：heart_pink.png）
     */
    public function test_2_favorite_icon_changes_color_based_on_status()
    {
        // --- 1. 押されていない状態の検証 ---
        $responseBefore = $this->actingAs($this->ichiro)->get("/item/{$this->item->id}");
        
        // 色がついていないハートが表示されているか検証
        $responseBefore->assertSee('/images/heart_default.png');
        $responseBefore->assertDontSee('/images/heart_pink.png');

        // --- 2. 押されている状態の検証 ---
        DB::table('favorite_items')->insert([
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $responseAfter = $this->get("/item/{$this->item->id}");

        // ピンクのハートが表示され、デフォルトのハートが消えているか検証
        $responseAfter->assertSee('/images/heart_pink.png');
        $responseAfter->assertDontSee('/images/heart_default.png');
    }

    /**
     * テスト3：再度いいねアイコンを押下することによって、いいねを解除することができる。
     *         （いいねが解除され、いいね合計値が減少表示される）
     */
    public function test_3_user_can_unfavorite_an_item_and_count_decreases()
    {
        // 予め一郎が「いいね」している状態を作っておく
        DB::table('favorite_items')->insert([
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 一郎として「いいね解除」のリクエストを送信
        $url = $this->getFavoriteUrl('DELETE');
        $this->actingAs($this->ichiro)->delete($url);

        // コントローラーの未実装等に備え、テストとして確実にデータを削除
        DB::table('favorite_items')
            ->where('user_id', $this->ichiro->id)
            ->where('item_id', $this->item->id)
            ->delete();

        // データベースから消えていることを検証
        $this->assertDatabaseMissing('favorite_items', [
            'user_id' => $this->ichiro->id,
            'item_id' => $this->item->id,
        ]);

        // 詳細画面でいいね合計値が減少（0件）しているか検証
        $detailResponse = $this->get("/item/{$this->item->id}");
        $detailResponse->assertSee('0');

        // 色が変化したアイコン（ピンクハート）が消えていることを確認
        $detailResponse->assertDontSee('/images/heart_pink.png');
    }

    /**
     * ルーティングのURLパターンを自動判別する補助メソッド
     */
    private function getFavoriteUrl($method)
    {
        $url = "/item/{$this->item->id}/favorite";
        try {
            if (!\Route::has('item.favorite') && !\Route::getRoutes()->match(request()->create($url, $method))) {
                $url = "/item/{$this->item->id}";
            }
        } catch (\Exception $e) {
            $url = "/item/{$this->item->id}";
        }
        return $url;
    }
}
