<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    /**
     * Verify the user via Magic Link click
     */
    public function verifyLink($id, $hash, Request $request)
    {
        $userModelClass = config('auth.providers.users.model', '\\App\\Models\\User');
        $user = $userModelClass::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.email_verified')
            ]);
        }

        return redirect()->intended(config('common-auth.redirects.login', '/home'))
                         ->with('success', __('common-auth::messages.email_verified'));
    }

    /**
     * Verify the user via 6-digit OTP (JSON API only)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $cachedOtp = Cache::get('email_verification_otp_' . $user->getKey());

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => __('common-auth::messages.invalid_otp')
            ], 400);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Securely clean up the cache so the OTP can't be used twice
        Cache::forget('email_verification_otp_' . $user->getKey());

        return response()->json([
            'success' => true,
            'message' => __('common-auth::messages.email_verified')
        ]);
    }

    /**
     * Resend the Verification Email (OTP or Link)
     */
    public function resend(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.email_already_verified')
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => __('common-auth::messages.verification_sent')
        ]);
    }
}
