<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $hash = hash('sha256', strtolower(trim($value)));
                    if (User::where('email_hash', $hash)->exists()) {
                        $fail('이미 등록된 이메일입니다.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $this->userService->register($validated);

        Auth::login($user);

        // 로컬 환경에서는 이메일 인증 자동 처리 (localhost / 127.0.0.1)
        if (in_array($request->getHost(), ['localhost', '127.0.0.1'])) {
            $user->markEmailAsVerified();
            return redirect(route('dashboard', absolute: false));
        }

        return redirect(route('verification.notice'));
    }
}
