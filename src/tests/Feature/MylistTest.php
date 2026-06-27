<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. いいねした商品だけが表示される
     */
    public function test_only_liked_items_are_displayed_on_mylist()
    {
        // ログインユーザーと、いいねする商品・しない商品を作成
        $user = User::factory()->create();
        $liked_item = Item::factory()->create(['item_name' => 'いいねした商品', 'sales_status' => 1]);
        $unliked_item = Item::factory()->create(['item_name' => 'いいねしていない商品', 'sales_status' => 1]);

        // favorite_items テーブルにいいねデータを登録
        \DB::table('favorite_items')->insert([
            'user_id' => $user->id,
            'item_id' => $liked_item->id,
        ]);

        // ログインしてマイリスト（タブ）を開く
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);

        // いいねした商品だけが表示されているか検証
        $response->assertSee($liked_item->item_name);
        $response->assertDontSee($unliked_item->item_name);
    }

    /**
     * 2. 購入済み商品は「SOLD」と表示される
     */
    public function test_sold_out_items_in_mylist_display_sold_label()
    {
        $user = User::factory()->create();
        
        // いいねした商品が購入済み（sales_status = 3）の状態を作成
        $sold_item = Item::factory()->create(['item_name' => '売切マイ商品', 'sales_status' => 3]);

        \DB::table('favorite_items')->insert([
            'user_id' => $user->id,
            'item_id' => $sold_item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);

        // マイリスト内でも「SOLD」の文字が表示されているか検証
        $response->assertSee('SOLD');
    }

    /**
     * 3. 未認証の場合は何も表示されない
     */
    public function test_nothing_is_displayed_on_mylist_when_guest()
    {
        // 商品がデータベースに存在している状態
        $item = Item::factory()->create(['item_name' => 'ある商品']);

        // ログインせずにマイリスト（タブ）を開く
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        // 未ログイン状態（ゲスト）なので、商品名が表示されていないことを検証
        $response->assertDontSee($item->item_name);
    }
}
