<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // コピー元のディレクトリ（public/images/profile_test/）
        $sourcePath = public_path('images/profile_test');
        
        // コピー先のストレージディレクトリ（storage/app/public/images/profile）
        $targetDir = 'images/profile';

        // コピー元が存在するかチェック
        if (!File::exists($sourcePath)) {
            // ログに出力するか、例外を投げて知らせる
            throw new \Exception("コピー元のディレクトリが見つかりません: " . $sourcePath);
        }

        // コピー先のディレクトリがなければ作成する
        if (!Storage::disk('public')->exists($targetDir)) {
            Storage::disk('public')->makeDirectory($targetDir);
        }

        $files = File::files($sourcePath);

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $targetPath = $targetDir . '/' . $fileName;
            Storage::disk('public')->put($targetPath, fopen($file->getRealPath(), 'r'));
        }

        // 4名分のユーザーデータを定義
        $users = [
            [
                'member_name'  => '鈴木一郎',
                'email'        => 'ichiro@example.com',
                'password'     => Hash::make('11111111'), // 暗号化して登録
                'member_image' => 'shingapore.jpg',
                'postcode'     => '1020012',
                'address'      => '東京都千代田区東町１−２−３',
                'building'     => '千代田プラザマンション３０５号室',
                'is_withdrawn' => 0,
                'email_verified_at' => now(), 
            ],
            [
                'member_name'  => '田中二郎',
                'email'        => 'jiro@example.com',
                'password'     => Hash::make('11111111'),
                'member_image' => 'thai.jpg',
                'postcode'     => '2030023',
                'address'      => '神奈川県横浜市港北区西１−２−３',
                'building'     => '港北タワーマンション４０２号室',
                'is_withdrawn' => 0,
                'email_verified_at' => now(), 
            ],
            [
                'member_name'  => '佐藤三郎',
                'email'        => 'saburo@example.com',
                'password'     => Hash::make('11111111'),
                'member_image' => 'beer.jpg',
                'postcode'     => '3040034',
                'address'      => '千葉県千葉市美浜区南町３−５−１',
                'building'     => '美浜ニュータウンマンション１０２号室',
                'is_withdrawn' => 0,
                'email_verified_at' => now(), 
            ],
            [
                'member_name'  => '小野四郎',
                'email'        => 'shiro@example.com',
                'password'     => Hash::make('11111111'),
                'member_image' => 'tomato.jpg',
                'postcode'     => '4050045',
                'address'      => '埼玉県さいたま市中央区北町４−７−３',
                'building'     => 'さいたまの森マンション３０６号室',
                'is_withdrawn' => 0,
                'email_verified_at' => now(), 
            ],
        ];

        // データベースに一括挿入
        DB::table('users')->insert($users);
    }
}
