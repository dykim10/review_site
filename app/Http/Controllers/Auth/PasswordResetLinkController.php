<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            app(PasswordResetService::class)->sendLinkToEmail($request->email);
        } catch (\Exception $e) {
            Log::error('비밀번호 재설정 이메일 발송 실패', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => '이메일 발송에 실패했습니다. 잠시 후 다시 시도해주세요.']);
        }

        return back()->with('status', __('passwords.sent'));
    }
}
