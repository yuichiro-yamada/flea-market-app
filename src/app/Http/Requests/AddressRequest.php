<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'shipping_postcode'    => ['required', 'string', 'digits:7'],
            'shipping_address'     => ['required', 'string', 'max:255'],
            'shipping_building'    => ['nullable', 'string', 'max:255'],
        ];
    }
    public function messages(): array
    {
        return [
            // 郵便番号
            'shipping_postcode.required' => '郵便番号を入力してください',
            'shipping_postcode.digits'     => '郵便番号はハイフンなしの7桁の数字で入力してください',

            // 住所
            'shipping_address.required'  => '住所を入力してください',
            'shipping_address.max'       => '住所は255文字以内で入力してください',

            // 建物名
            'shipping_building.max'      => '建物名は255文字以内で入力してください',
        ];
    }
}
