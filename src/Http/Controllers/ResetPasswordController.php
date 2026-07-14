<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Arjunyuvanesh\CommonAuth\Http\Requests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request): JsonResponse|RedirectResponse
    {
        $status = Password::broker()->reset(
            $request->validated(),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
                
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => __($status)]);
            }
            return redirect(config('common-auth.redirects.password_reset', '/login'))->with('status', __($status));
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => __($status)], 400);
        }
        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
