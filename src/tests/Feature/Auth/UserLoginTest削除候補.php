<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    // テスト実行ごとにデータベースをリセットする
    use RefreshDatabase;

    /**
     * 1. メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_when_email_is_empty()
    {
        $response = $this->post('/login', [
            'email' => '', // 空欄
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * 2. パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_when_password_is_empty()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', // 空欄
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * 3. 入力情報が間違っている場合、バリデーションメッセージが表示される
     */
    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong-user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    /**
     * 4. 正しい情報が入力された場合、ログイン処理が実行される
     */
    public function test_users_can_authenticate_with_valid_credentials()
    {
        // テスト用のユーザーをデータベースに1件登録しておく
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // 正しい情報でログインを試みる
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // 【機能検証】ログイン処理（認証）が成功しているか確認
        $this->assertAuthenticated();

        // 【遷移検証】ログイン後にプロフィール画面へ遷移するか確認
        $response->assertRedirect('/mypage/profile');
    }
}
