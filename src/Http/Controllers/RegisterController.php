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
                $payload = [
                    'success' => true,
                    'message' => __('common-auth::messages.register_success'),
                    'user'    => new \Arjunyuvanesh\CommonAuth\Http\Resources\UserResource($user)
                ];

                // Optional Laravel Sanctum support for Mobile Apps
                if (method_exists($user, 'createToken')) {
                    $payload['token'] = $user->createToken('auth_token')->plainTextToken;
                }

                return response()->json($payload, 201);
            }

            return redirect()->intended(config('common-auth.redirects.register', '/home'))
                             ->with('success', __('common-auth::messages.register_success'));

        } catch (\Arjunyuvanesh\CommonAuth\Exceptions\RegistrationFailedException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() // This message is already localized from the Service
                ], 400);
            }

            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            
        } catch (Exception $e) {
            // Fallback for any other unexpected system crashes
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('common-auth::messages.register_failed')
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => __('common-auth::messages.register_failed')]);
        }
    }
}
