<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Arjunyuvanesh\CommonAuth\Http\Requests\LoginRequest;
use Arjunyuvanesh\CommonAuth\Contracts\AuthServiceInterface;

class LoginController extends Controller
{
    protected $authService;

    /**
     * Inject the interface, NOT the concrete class! (Dependency Inversion)
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $remember = $request->boolean('remember');

        // This will automatically throw a ValidationException (triggering a redirect back with errors, 
        // or returning a 422 JSON error) if the user is not found OR the password is wrong!
        $this->authService->attemptLogin($request->validated(), $remember);
            
        $request->session()->regenerate();
        
        // Content Negotiation: API vs Web
        if ($request->wantsJson()) {
            // Because login manually used attemptLogin (which uses the guard), we can safely fetch the user from the guard here for the initial login response
            $user = auth()->guard(config('common-auth.guard', 'web'))->user();
            
            $payload = [
                'success' => true,
                'message' => __('common-auth::messages.login_success'),
                'user'    => new \Arjunyuvanesh\CommonAuth\Http\Resources\UserResource($user)
            ];

            // Optional Laravel Sanctum support for Mobile Apps
            if (method_exists($user, 'createToken')) {
                $payload['token'] = $user->createToken('auth_token')->plainTextToken;
            }
            
            return response()->json($payload);
        }

        return redirect()->intended(config('common-auth.redirects.login', '/home'))
                            ->with('success', __('common-auth::messages.login_success'));
    }

    /**
     * Get the authenticated User profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'user'    => new \Arjunyuvanesh\CommonAuth\Http\Resources\UserResource($user)
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        // 1. If they are hitting an API endpoint, securely revoke their API token!
        if ($user = $request->user()) {
            if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
        }

        // 2. Destroy the web session (if any)
        $this->authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('common-auth::messages.logout_success')
            ]);
        }

        return redirect(config('common-auth.redirects.logout', '/'))->with('success', __('common-auth::messages.logout_success'));
    }
}
