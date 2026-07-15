<?php

namespace Arjunyuvanesh\CommonAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        // Dynamically fetch the rules from the configuration file.
        // We provide a fallback array just in case the config is missing.
        $rules = config('common-auth.registration_fields', [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
        ]);

        // Security: Override whatever weak string might be in the config file
        // and forcefully apply our enterprise-grade password defaults!
        $rules['password'] = ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique'    => __('common-auth::messages.email_taken'),
            'mobile.unique'   => __('common-auth::messages.mobile_taken'),
            'username.unique' => __('common-auth::messages.username_taken'),
        ];
    }
}
