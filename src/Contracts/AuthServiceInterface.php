<?php

namespace Arjunyuvanesh\CommonAuth\Contracts;

use Illuminate\Database\Eloquent\Model;

interface AuthServiceInterface
{
    /**
     * Attempt to log a user in using email, mobile, or username.
     *
     * @param array $credentials
     * @param bool $remember
     * @return bool
     */
    public function attemptLogin(array $credentials, bool $remember = false): bool;

    /**
     * Register a new user dynamically and assign the default Spatie role.
     *
     * @param array $data
     * @return Model
     */
    public function registerUser(array $data): Model;

    /**
     * Logout the currently authenticated user.
     * 
     * @return void
     */
    public function logout(): void;
}
