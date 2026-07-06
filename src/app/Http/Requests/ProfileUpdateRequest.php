<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // 画像選択時
        if ($this->input('action') === 'select_image') {
            return [
                'member_image' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg',
                    'max:2048',
                ],
            ];
        }
        // 更新ボタン押下時
        return [
            'member_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'member_name' => ['required', 'string', 'max:255'],
            'postcode'    => ['nullable', 'string', 'digits:7'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ];
    }
    public function messages(): array
    {
        return [
            // 商品画像
            'member_image.image'  => '指定されたファイルは画像ではありません',
            'member_image.mimes'  => '画像はjpeg、png、jpg形式のみ対応しています',
            'member_image.max'    => '画像サイズは2MB以内でアップロードしてください',

            // ユーザー名
            'member_name.required' => 'お名前を入力してください',
            'member_name.max'      => 'お名前は20文字以内で入力してください',

            // 郵便番号
            'postcode.digits'     => '郵便番号はハイフンなしの7桁の数字で入力してください',

            // 住所
            'address.max'       => '住所は255文字以内で入力してください',

            // 建物名
            'building.max'      => '建物名は255文字以内で入力してください',
        ];
    }
}
