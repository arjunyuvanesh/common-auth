<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Arjunyuvanesh\CommonAuth\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Update the authenticated user's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        // Dynamically update only the fields that were submitted
        $user->fill($request->validated());

        // If they change their email address, we must revoke their verification status
        if ($user->isDirty('email') && method_exists($user, 'hasVerifiedEmail')) {
            $user->email_verified_at = null;
            // The frontend should ideally prompt them to verify the new email, or we could automatically send the notification here:
            // $user->sendEmailVerificationNotification();
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.profile_updated'),
                'user'    => new \Arjunyuvanesh\CommonAuth\Http\Resources\UserResource($user)
            ]);
        }

        return back()->with('success', __('common-auth::messages.profile_updated'));
    }

    /**
     * Permanently delete the authenticated user's account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // 1. Revoke API tokens BEFORE deleting the user so they can't be orphaned
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // 2. Delete the actual database record
        $user->delete();

        // 3. Log out of the web session securely
        Auth::guard(config('common-auth.guard', 'web'))->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.account_deleted')
            ]);
        }

        return redirect('/')->with('success', __('common-auth::messages.account_deleted'));
    }
}
