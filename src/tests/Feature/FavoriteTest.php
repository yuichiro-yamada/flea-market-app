<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. いいねアイコンを押下することによって、いいねした商品として登録することができる
     */
    public function test_1_いいねアイコンを押下することによって、いいねした商品として登録することができる(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログインして商品詳細ページを開く（初期状態の確認）
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('0');

        // ⭕ ルーティングに合わせてURLを変更（末尾に /favorite を追加）
        $response = $this->post("/item/{$item->id}/favorite");

        // favorite_itemsテーブルにデータが登録されたか検証
        $this->assertDatabaseHas('favorite_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 再度詳細ページを開いて、いいね合計値が「1」に増加しているか検証
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('1');
    }

    /**
     * 2. 追加済みのアイコンは色が変化する
     */
    public function test_2_追加済みのアイコンは色が変化する(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ログインして商品詳細ページを開く（まだいいねしていない状態）
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('/images/heart_default.png');
        $response->assertDontSee('/images/heart_pink.png');

        // ⭕ ルーティングに合わせてURLを変更
        $this->post("/item/{$item->id}/favorite");

        // 再度詳細ページを開く（いいねした状態）
        $response = $this->get("/item/{$item->id}");
        
        // ピンクのハート画像に変化しているか検証
        $response->assertSee('/images/heart_pink.png');
        $response->assertDontSee('/images/heart_default.png');
    }

    /**
     * 3. 再度いいねアイコンを押下することによって、いいねを解除することができる
     */
    public function test_3_再度いいねアイコンを押下することによって、いいねを解除することができる(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // あらかじめいいねが登録されている状態を作る
        \DB::table('favorite_items')->insert([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // ログインして商品詳細ページを開く（すでにいいねしている状態、カウントは1）
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('1');
        $response->assertSee('/images/heart_pink.png');

        // ⭕ ルーティングに合わせてURLを変更（DELETEメソッドはそのまま）
        $response = $this->delete("/item/{$item->id}/favorite");

        // favorite_itemsテーブルからデータが消えたか検証
        $this->assertDatabaseMissing('favorite_items', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // 再度詳細ページを開いて、いいね合計値が「0」に減少し、アイコンが戻っているか検証
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('0');
        $response->assertSee('/images/heart_default.png');
    }
}
