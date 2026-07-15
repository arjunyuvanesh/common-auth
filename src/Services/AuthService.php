<?php

namespace Arjunyuvanesh\CommonAuth\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Arjunyuvanesh\CommonAuth\Contracts\AuthServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthService implements AuthServiceInterface
{
    /**
     * Attempt to log a user in using email, mobile, or username.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool
    {
        $loginField = $credentials['login'];
        $password   = $credentials['password'];
        
        $fieldType = $this->determineLoginField($loginField);
        $guard = config('common-auth.guard', 'web');
        $userModelClass = config('auth.providers.users.model', '\\App\\Models\\User');

        // 1. Check if the user exists
        $user = $userModelClass::where($fieldType, $loginField)->first();

        if (!$user) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'login' => __('common-auth::messages.user_not_found')
            ]);
        }

        // 2. Check if the password is correct
        if (!Hash::check($password, $user->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => __('common-auth::messages.incorrect_password')
            ]);
        }

        // 3. Log the user in (with optional Remember Me support)
        Auth::guard($guard)->login($user, $remember);
        
        return true;
    }

    /**
     * Register a new user dynamically, assign a role, wrapped in a crash-proof transaction.
     *
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function registerUser(array $data): Model
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        try {
            // DB::transaction ensures that if assigning the role fails, 
            // the user insertion is safely rolled back!
            return DB::transaction(function () use ($data) {
                $userModelClass = config('auth.providers.users.model', '\\App\\Models\\User');
                
                /** @var Model $user */
                $user = $userModelClass::create($data);

                $defaultRole = config('common-auth.default_role');
                
                if ($defaultRole && method_exists($user, 'assignRole')) {
                    $user->assignRole($defaultRole);
                }

                return $user;
            });
            
        } catch (Exception $e) {
            // Log the exact error for server admins, throw a safe custom error for the frontend
            Log::error('CommonAuth Registration Failed: ' . $e->getMessage());
            throw new \Arjunyuvanesh\CommonAuth\Exceptions\RegistrationFailedException(__('common-auth::messages.register_failed'));
        }
    }

    /**
     * Logout the currently authenticated user.
     * 
     * @return void
     */
    public function logout(): void
    {
        $guard = config('common-auth.guard', 'web');
        Auth::guard($guard)->logout();
    }

    /**
     * Determine if the given string is an email, mobile, or username.
     *
     * @param string $login
     * @return string
     */
    protected function determineLoginField(string $login): string
    {
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        if (is_numeric($login)) {
            return 'mobile';
        }
        
        return 'username';
    }
}
