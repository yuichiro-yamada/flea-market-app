<?php

namespace Tests\Feature\items;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. 全商品を取得できる
     */
    public function test_all_items_are_displayed_on_homepage()
    {
        $item1 = Item::factory()->create(['item_name' => '商品A', 'sales_status' => 1]);
        $item2 = Item::factory()->create(['item_name' => '商品B', 'sales_status' => 1]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee($item1->item_name);
        $response->assertSee($item2->item_name);
    }

    /**
     * 2. 購入済み商品は「SOLD」と表示される
     */
    public function test_sold_out_items_display_sold_label()
    {
        $sold_item = Item::factory()->create(['item_name' => '売り切れ商品', 'sales_status' => 3]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // ⭕ 仕様に合わせて「SOLD」を検証するように修正
        $response->assertSee('SOLD');
    }

    /**
     * 3. 自分が出品した商品は表示されない
     */
    public function test_my_own_items_are_not_displayed_when_logged_in()
    {
        $me = User::factory()->create();
        $other_user = User::factory()->create();

        $my_item = Item::factory()->create(['item_name' => '私の出品商品', 'seller_id' => $me->id, 'sales_status' => 1]);
        $other_item = Item::factory()->create(['item_name' => '他人の出品商品', 'seller_id' => $other_user->id, 'sales_status' => 1]);

        $response = $this->actingAs($me)->get('/');
        $response->assertStatus(200);

        $response->assertSee($other_item->item_name);
        $response->assertDontSee($my_item->item_name);
    }

    /**
     * 4. 「商品名」で部分一致検索ができる
     */
    public function test_items_can_be_searched_by_name_partially()
    {
        $match_item = Item::factory()->create(['item_name' => '最新のスマートフォン', 'sales_status' => 1]);
        $unmatch_item = Item::factory()->create(['item_name' => 'クラシックな時計', 'sales_status' => 1]);

        // ⭕ パラメーター名を「keyword」に修正
        $response = $this->get('/?keyword=スマート');
        $response->assertStatus(200);

        $response->assertSee($match_item->item_name);
        $response->assertDontSee($unmatch_item->item_name);
    }

    /**
     * 5. 検索状態がマイリストでも保持されている
     */
    public function test_search_keyword_is_retained_on_mylist_tab()
    {
        // ⭕ パラメーター名を「keyword」に修正
        $response = $this->get('/?keyword=スマート&tab=mylist');
        $response->assertStatus(200);

        $response->assertSee('スマート');
    }

    /**
     * 6. 必要な情報が表示される（詳細ページ）
     */
    public function test_item_detail_page_displays_all_necessary_information()
    {
        $seller = User::factory()->create();
        $comment_user = User::factory()->create(['member_name' => 'コメント太郎']);
        
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'item_name' => '最高級のペン',
            'brand_name' => 'パイロット',
            'item_price' => 5000,
            'item_detail' => 'これは素晴らしい書き心地のペンです。',
            'condition' => 1,
        ]);

        \DB::table('favorite_items')->insert([
            'user_id' => $comment_user->id,
            'item_id' => $item->id,
        ]);

        \DB::table('reviews')->insert([
            'user_id' => $comment_user->id,
            'item_id' => $item->id,
            'comment' => 'とても使いやすいです！',
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee($item->item_name);
        $response->assertSee($item->brand_name);
        $response->assertSee('5,000'); // カンマ表記がない場合は '5000' に変更してください
        $response->assertSee($item->item_detail);
        $response->assertSee('とても使いやすいです！');
        $response->assertSee('コメント太郎');
        $response->assertSee('1'); 
    }

    /**
     * 7. 複数選択されたカテゴリが表示されているか
     */
    public function test_multiple_selected_categories_are_displayed_on_detail_page()
    {
        $category1_id = \DB::table('categories')->insertGetId(['category_name' => 'ファッション']);
        $category2_id = \DB::table('categories')->insertGetId(['category_name' => 'メンズ']);

        $item = Item::factory()->create();

        \DB::table('item_categories')->insert([
            ['item_id' => $item->id, 'category_id' => $category1_id],
            ['item_id' => $item->id, 'category_id' => $category2_id],
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
    }
}
