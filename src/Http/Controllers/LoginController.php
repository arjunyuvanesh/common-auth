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
        if ($this->authService->attemptLogin($request->validated())) {
            
            $request->session()->regenerate();
            
            // Content Negotiation: API vs Web
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully logged in.',
                    'user'    => auth()->guard(config('common-auth.guard', 'web'))->user()
                ]);
            }

            return redirect()->intended(config('common-auth.redirects.login', '/home'))
                             ->with('success', 'Successfully logged in.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        $this->authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.'
            ]);
        }

        return redirect(config('common-auth.redirects.logout', '/'))->with('success', 'Successfully logged out.');
    }
}
