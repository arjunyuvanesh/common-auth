<?php

namespace Arjunyuvanesh\CommonAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guard(config('common-auth.guard', 'web'))->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Laravel's native current_password rule automatically verifies against the DB!
            'current_password' => ['required', 'string', 'current_password:' . config('common-auth.guard', 'web')],
            'password'         => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }
}
