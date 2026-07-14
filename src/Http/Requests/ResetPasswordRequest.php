<?php

namespace Arjunyuvanesh\CommonAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token'    => 'required|string',
            'email'    => 'required|email',
            // Using Laravel's Password::defaults() ensures the new password meets
            // the strict security standards defined globally by the host application
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
