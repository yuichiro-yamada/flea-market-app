<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        // 1つ以上の選択を必須（required）にし、配列（array）であることを検証
        'category_ids' => ['required', 'array', 'min:1'],
        // カテゴリIDの各中身が categories テーブルに存在するかを個別にチェック
        'category_ids.*' => ['exists:categories,id'],

        // 商品の状態（例: 選択ボックスの値は数値になるため integer を指定）
        'condition' => ['required', 'integer'],

        // 商品名
        'item_name' => ['required', 'string', 'max:255'],

        // 画像ファイル（最大2MBまで）
        // 一時保存パス（item_tmp_image_path）がない時だけ必須
        'item_image' => 'required_without:item_tmp_image_path|image|mimes:jpeg,png,jpg,gif|max:2048',
        
        // 一時保存の画像パスが届く可能性もあるため、ルールを定義しておく
        'item_tmp_image_path' => 'nullable|string',

        // ブランド
        'brand_name' => ['nullable','string', 'max:255'],

        // 説明（1000文字まで）
        'item_detail' => ['required', 'string', 'max:1000'],

        // 販売価格（最低50円〜最大約1000万円までに制限）
        'item_price' => ['required', 'integer', 'min:50', 'max:9999999'],
        ];
    }

    public function messages(): array
    {
        return [
            // 商品画像
            'item_image.required_without' => '商品画像をアップロードしてください', // 画像の一時保存だけではチェックが走らない
            'item_image.image'            => '指定されたファイルは画像ではありません',
            'item_image.mimes'            => '画像はjpeg、png、jpg形式のみ対応しています',
            'item_image.max'              => '画像サイズは2MB以内でアップロードしてください',

            // カテゴリ
            'category_ids.required' => 'カテゴリーを1つ以上選択してください',

            // 商品の状態
            'condition.required' => '商品の状態を選択してください',

            // 商品名
            'item_name.required'    => '商品名を入力してください',
            'item_name.max'         => '商品名は255文字以内で入力してください',

            // ブランド名
            'brand_name.max'         => 'ブランド名は255文字以内で入力してください',

            // 商品の説明
            'item_detail.required'  => '商品の説明を入力してください',
            'item_detail.max'       => '商品の説明は1000文字以内で入力してください',

            // 販売価格
            'item_price.required'   => '販売価格を入力してください',
            'item_price.integer'    => '販売価格は半角数字で入力してください',
            'item_price.min'        => '販売価格は50円以上で入力してください',
            'item_price.max'        => '販売価格は9,999,999円以内で入力してください',
        ];
    }
}




