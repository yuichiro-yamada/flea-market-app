<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth; 

class ReviewRequest extends FormRequest
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
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => "コメントを入力してください",
            'comment.max' => "コメントは255文字以内で入力してください"
        ];
    }

    // エラーメッセージをモーダルで表示する
    protected function failedValidation(Validator $validator)
    {
        // 最初のエラーメッセージを取得（例: "コメントは必ず入力してください。"）
        $errorMessage = $validator->errors()->first('comment');

        throw new HttpResponseException(
            back()->withInput() // 入力値を残す
                ->with('modal_message', $errorMessage)
        );
    }
}
