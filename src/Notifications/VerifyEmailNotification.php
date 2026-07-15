<?php

namespace Arjunyuvanesh\CommonAuth\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailNotification extends BaseVerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $method = config('common-auth.verification_method', 'both');

        $mail = (new MailMessage)
            ->subject(__('common-auth::messages.verify_email_subject'))
            ->line(__('common-auth::messages.verify_email_intro'));

        // Handle OTP Logic
        if (in_array($method, ['otp', 'both'])) {
            $otp = random_int(100000, 999999);
            // Cache the OTP for 15 minutes, keyed by user ID
            Cache::put('email_verification_otp_' . $notifiable->getKey(), $otp, now()->addMinutes(15));
            
            $mail->line(__('common-auth::messages.verify_email_otp_label') . ' **' . $otp . '**');
            $mail->line(__('common-auth::messages.verify_email_otp_expires'));
        }

        // Handle Link Logic
        if (in_array($method, ['link', 'both'])) {
            $verificationUrl = $this->verificationUrl($notifiable);
            $mail->action(__('common-auth::messages.verify_email_action'), $verificationUrl);
        }

        return $mail;
    }

    /**
     * Get the verification URL for the given notifiable.
     * We override this to point to our package's specific verification route.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'common-auth.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
