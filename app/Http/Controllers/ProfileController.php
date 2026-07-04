<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\PasswordResetService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->userService->updateProfile($request->user(), $request->validated());

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        try {
            app(PasswordResetService::class)->sendLinkToUser($request->user());
        } catch (\Exception $e) {
            Log::error('프로필 비밀번호 재설정 이메일 발송 실패', [
                'user_id' => $request->user()->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors([
                'password_reset' => '이메일 발송에 실패했습니다. 잠시 후 다시 시도해주세요.',
            ]);
        }

        return redirect()->route('profile.edit')->with('status', 'password-reset-link-sent');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $this->userService->deleteAccount($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
