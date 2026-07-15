<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Arjunyuvanesh\CommonAuth\Http\Requests\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    /**
     * Update the user's password securely.
     */
    public function update(ChangePasswordRequest $request)
    {
        $user = $request->user();

        // Update the password
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // Content Negotiation: API vs Web
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.password_changed')
            ]);
        }

        return back()->with('success', __('common-auth::messages.password_changed'));
    }
}
