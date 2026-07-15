<?php

namespace Arjunyuvanesh\CommonAuth\Traits;

use Spatie\Permission\Traits\HasRoles;

trait HasCommonAuth
{
    // Automatically inject Spatie's permission logic into the host model
    use HasRoles;

    /**
     * Get the user's primary login identifier.
     * Since users can login with username, email, or mobile, this helper
     * guarantees you always have a fallback identifier to display on the frontend.
     *
     * @return string|null
     */
    public function getLoginIdentifierAttribute()
    {
        return $this->username ?? $this->email ?? $this->mobile;
    }

    /**
     * Send the email verification notification.
     * We override this to seamlessly support OTP, Links, or Both based on our package config!
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $method = config('common-auth.verification_method', 'none');
        
        if ($method !== 'none') {
            $this->notify(new \Arjunyuvanesh\CommonAuth\Notifications\VerifyEmailNotification());
        }
    }
}
