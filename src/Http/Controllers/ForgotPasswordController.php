<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Arjunyuvanesh\CommonAuth\Http\Requests\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request): JsonResponse|RedirectResponse
    {
        $status = Password::broker()->sendResetLink(
            $request->validated()
        );

        if ($status === Password::RESET_LINK_SENT) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => __($status)]);
            }
            return back()->with('status', __($status));
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => __($status)], 400);
        }
        return back()->withErrors(['email' => __($status)]);
    }
}
