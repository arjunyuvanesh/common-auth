<?php

namespace Arjunyuvanesh\CommonAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Arjunyuvanesh\CommonAuth\Http\Requests\RegisterRequest;
use Arjunyuvanesh\CommonAuth\Contracts\AuthServiceInterface;
use Exception;

class RegisterController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $user = $this->authService->registerUser($request->validated());

            event(new Registered($user));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful.',
                    'user'    => $user
                ], 201);
            }

            return redirect()->intended(config('common-auth.redirects.register', '/home'))
                             ->with('success', 'Registration successful.');

        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
