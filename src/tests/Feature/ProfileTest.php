<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\SalesRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_1_必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）(): void
    {
        // テスト用ユーザーの作成（カラム名はmember_name）
        $user = User::factory()->create([
            'member_name' => 'マイページ太郎',
            'member_image' => 'shingapore.jpg',
        ]);

        // 他のユーザーを作成
        $other_user = User::factory()->create();

        // 自分が「出品した」商品と、他人が出品した商品を作成
        $my_sell_item = Item::factory()->create([
            'item_name' => '私が出品した商品A', 
            'seller_id' => $user->id,
        ]);
        $other_sell_item = Item::factory()->create([
            'item_name' => '他人が出品した商品X', 
            'seller_id' => $other_user->id,
            ]);

        // 自分が「購入した」商品の作成（SalesRecordテーブルと紐付け）
        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);

        $my_buy_item = Item::factory()->create([
            'item_name' => '私が購入した商品B', 
            'seller_id' => $other_user->id,
            'buyer_id' => $user->id,
        ]);
        SalesRecord::factory()->create([
            'buyer_id' => $user->id,
            'item_id' => $my_buy_item->id,
        ]);

        // --- 検証A: マイページ基本情報と「出品した商品一覧」の確認 ---
        // 通常のマイページ（または出品パラメータを付与したURL）を開く
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        // プロフィール画像、ユーザー名、自分が出品した商品が表示されているか
        $response->assertSee('shingapore.jpg');
        $response->assertSee('マイページ太郎');
        $response->assertSee('私が出品した商品A');
        $response->assertDontSee('他人が出品した商品X'); // 他人の商品は見えないこと

        // --- 検証B: 「購入した商品一覧」の確認 ---
        // 購入した商品一覧を表示（過去のテスト仕様 ?page=buy に合わせる。異なる場合は ?tab=buy 等に調整してください）
        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);

        // 自分が購入した商品が表示されているか
        $response->assertSee('私が購入した商品B');
    }

    /**
     * 2. 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_2_変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）(): void
    {
        // 既存のユーザー情報を設定して作成
        $user = User::factory()->create([
            'member_name' => '編集次郎',
            'member_image' => 'shingapore.jpg',
            'postcode' => '9876543',
            'address' => '大阪府大阪市北区梅田',
        ]);

        // プロフィール編集画面を開く
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);

        // 各入力フォームの初期値（value属性や画像パスなど）として設定されているか検証
        $response->assertSee('shingapore.jpg');
        $response->assertSee('編集次郎');
        $response->assertSee('9876543');
        $response->assertSee('大阪府大阪市北区梅田');
    }
}
