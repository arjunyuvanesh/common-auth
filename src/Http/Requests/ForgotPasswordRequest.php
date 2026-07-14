<?php

namespace Arjunyuvanesh\CommonAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Anyone can request a password reset link
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Standard Laravel password resets require an email.
            // Even if they log in via mobile/username, they must provide the email
            // tied to their account to receive the secure reset link.
            'email' => 'required|email',
        ];
    }
}
