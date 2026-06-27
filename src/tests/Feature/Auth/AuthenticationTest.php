<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_email_is_required_for_login()
    {
        // ログイン画面を開く
        $response = $this->get('/login');
        $response->assertStatus(200);

        // メールアドレスを空にしてPOST送信
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        // バリデーションエラーがあるか検証
        $response->assertSessionHasErrors(['email']);
        
        // ログイン画面へ戻った後、メッセージが表示されているか検証
        $response = $this->get('/login');
        $response->assertSee('メールアドレスを入力してください');
    }

    /**
     * 2. パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_password_is_required_for_login()
    {
        // パスワードを空にしてPOST送信
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // バリデーションエラーがあるか検証
        $response->assertSessionHasErrors(['password']);

        // ログイン画面へ戻った後、メッセージが表示されているか検証
        $response = $this->get('/login');
        $response->assertSee('パスワードを入力してください');
    }

    /**
     * 3. 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_invalid_credentials_display_error_message()
    {
        // 登録されていない情報をPOST送信
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        // 認証失敗のエラーメッセージがセッションにあるか検証（Laravelデフォルトのauth.failed等、または設定したキー）
        $response->assertSessionHasErrors();

        // ログイン画面へ戻った後、指定のメッセージが表示されているか検証
        $response = $this->get('/login');
        $response->assertSee('ログイン情報が登録されていません');
    }

    /**
     * 4. 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_valid_credentials_can_login()
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'email' => 'auth_test@example.com',
            'password' => Hash::make('password123'), // パスワードをハッシュ化して保存
        ]);

        // 正しい情報を入力してPOST送信
        $response = $this->post('/login', [
            'email' => 'auth_test@example.com',
            'password' => 'password123',
        ]);

        // 指定のリダイレクト先（http://localhost/）に遷移するか検証
        $response->assertRedirect('/');

        // ユーザーが認証（ログイン状態）されているか検証
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 5. ログアウトができる
     */
    public function test_user_can_logout()
    {
        // テスト用のユーザーを作成してログイン状態にする
        $user = User::factory()->create();
        $this->actingAs($user);

        // ログアウトURLにPOST送信
        $response = $this->post('/logout');

        // ログアウト後の遷移先（通常はトップ画面やログイン画面）を検証
        // ※もし別の場所に遷移する場合は適宜URLを変更してください
        $response->assertRedirect('/');

        // ユーザーがログアウト（未認証状態）されているか検証
        $this->assertGuest();
    }
}
