<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. ログイン済みのユーザーはコメントを送信できる
     */
    public function test_1_ログイン済みのユーザーはコメントを送信できる(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['sales_status' => 1]); // 販売中の商品

        // ログインして商品詳細ページを開く（初期状態はコメント0件）
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertStatus(200);

        // コメントを入力して送信
        // 💡 Bladeのroute('comments.store')に合わせてURLを定義しています。もし異なる場合は調整してください
        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'テストコメントです。素晴らしい商品ですね！',
        ]);

        // reviews テーブルにデータが保存されたか検証
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメントです。素晴らしい商品ですね！',
        ]);

        // 再度詳細ページを開いて、コメント（数や内容）が反映されているか検証
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('テストコメントです。素晴らしい商品ですね！');
    }

    /**
     * 2. ログイン前のユーザーはコメントを送信できない
     */
    public function test_2_ログイン前のユーザーはコメントを送信できない(): void
    {
        $item = Item::factory()->create(['sales_status' => 1]);

        // ログインせずにコメントをPOST送信
        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => '未ログインのコメント',
        ]);

        // ログイン画面へリダイレクトされる、または未認証エラーになることを検証
        $response->assertRedirect('/login');

        // reviews テーブルにデータが保存されていない（空である）ことを検証
        $this->assertDatabaseMissing('reviews', [
            'item_id' => $item->id,
            'comment' => '未ログインのコメント',
        ]);
    }

    /**
     * 3. コメントが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_3_メントが入力されていない場合、バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['sales_status' => 1]);

        // ログインして、コメントを空欄にして送信
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '',
        ]);

        // セッションに comment のバリデーションエラーがあるか検証
        $response->assertSessionHas('modal_message', 'コメントを入力してください');

        // リダイレクト先（詳細ページ）で日本語のメッセージが表示されているか検証
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('コメントを入力してください');
    }

    /**
     * 4. コメントが255字以上の場合、バリデーションメッセージが表示される
     */
    public function test_4_コメントが255字以上の場合、バリデーションメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['sales_status' => 1]);

        // 256文字のコメントを作成
        $long_comment = str_repeat('あ', 256);

        // ログインして、256文字のコメントを送信
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => $long_comment,
        ]);

        // セッションに comment のバリデーションエラーがあるか検証
        $response->assertSessionHas('modal_message', 'コメントは255文字以内で入力してください');

        // リダイレクト先（詳細ページ）で日本語のメッセージが表示されているか検証
        $response = $this->get("/item/{$item->id}");
        $response->assertSee('コメントは255文字以内で入力してください');
    }
}
