<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト1 名前が入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_1_名前が入力されていない場合_バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', [
            'member_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['member_name' => 'お名前を入力してください']);
    }

    /**
     * テスト2 メールアドレスが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_2_メールアドレスが入力されていない場合_バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * テスト3 パスワードが入力されていない場合、バリデーションメッセージが表示される
     */
    public function test_3_パスワードが入力されていない場合_バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * テスト4 パスワードが7文字以下の場合、バリデーションメッセージが表示される
     */
    public function test_4_パスワードが7文字以下の場合_バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'pass7文字',
            'password_confirmation' => 'pass7文字',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * テスト5 パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
     */
    public function test_5_パスワードが確認用パスワードと一致しない場合_バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /**
     * テスト6 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     */
    public function test_6_全ての項目が入力されている場合_会員情報が登録され_プロフィール設定画面に遷移される(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/mypage/profile');
    }

    /**
     * テスト7 会員登録後、認証メールが送信される
     */
    public function test_7_会員登録後_認証メールが送信される(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'member_name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/mypage/profile');

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo([$user], VerifyEmail::class);
    }

    /**
     * テスト8 メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function test_8_メール認証誘導画面で_認証はこちらから_ボタンを押下するとメール認証サイトに遷移する(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        
        $response->assertStatus(302);
    }

    /**
     * テスト9 メール認証サイトのメール認証を完了すると、プロフィール設定画面に遷移する
     */
    public function test_9_メール認証サイトのメール認証を完了すると_プロフィール設定画面に遷移する(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // リダイレクト先のURLに指定の文字が含まれているか検証
        $this->assertStringContainsString('/mypage/profile', $response->headers->get('Location'));
    }
}
