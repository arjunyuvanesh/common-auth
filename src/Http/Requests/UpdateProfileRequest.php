<?php

namespace Arjunyuvanesh\CommonAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = $this->user();
        
        // Dynamically fetch the table name to ensure the unique validation targets the correct table
        $userModelClass = config('auth.providers.users.model', '\\App\\Models\\User');
        $tableName = (new $userModelClass)->getTable();

        // Using "sometimes" means these fields are only validated if they are present in the request payload
        // Using "ignore($user->id)" allows the user to submit their current email without triggering a "taken" error
        return [
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique($tableName)->ignore($user->id)],
            'mobile'   => ['sometimes', 'required', 'string', 'max:20', Rule::unique($tableName)->ignore($user->id)],
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique($tableName)->ignore($user->id)],
        ];
    }
}
