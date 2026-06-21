// ⭐️⭐️⭐️⭐️⭐️⭐️
// ⭐️テストをする際はscr/config/database.phpを修正する
// 「Pdo\Mysql::ATTR_SSL_CA」PHP 8.5(2025年末登場)の新しい書き方(テスト用)
// 「PDO::MYSQL_ATTR_SSL_CA」こちらは古い書き方（通常時用）
// 「これからは新しい書き方（Pdo\Mysql::ATTR_SSL_CA）にしてね」という注意書き（Deprecated）を出すようになりました。
// Laravel本体やPHPの拡張機能側で対応できているものとできていないものが混在しているため、テスト時のみ書き換える。
// 通常時用にしておかないと php artisan migrate:refresh --seed のコマンドが実行できない
// ⭐️⭐️⭐️⭐️⭐️⭐️

<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    // テスト実行ごとにデータベースをまっさらにしてリセットする便利な機能
    use RefreshDatabase;

    /**
     * Breezeの登録画面が表示されるかテスト
     */
    public function test_registration_screen_can_be_rendered()
    {
        // /register にアクセス（GETリクエスト）してみる
        $response = $this->get('/register');

        // 画面が無事に表示された（ステータスコード200）ことを確認
        $response->assertStatus(200);
    }

   /**
     * 実際にユーザー登録が成功し、未認証ならプロフィール画面から追い返されるかテスト
     */
    public function test_new_users_can_register()
    {
        // ユーザー登録を実行
        $response = $this->post('/register', [
            'member_name' => 'フリマ太郎',
            'email' => 'furima@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 登録直後のプログラムの命令は、設定通り /mypage/profile への移動であることを確認
        $response->assertRedirect('/mypage/profile'); 

        // 自動ログインされた状態で、実際にプロフィール画面にアクセスしてみる
        $profileResponse = $this->get('/mypage/profile');

        // まだメール認証が「未完了」なので、門番（verifiedミドルウェア）に弾かれて
        // メール認証画面（/verify-email）にリダイレクトされるかを確認（デバッグ）
        $profileResponse->assertRedirect('/verify-email');
    }
}
