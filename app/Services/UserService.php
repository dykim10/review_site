<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * 회원가입 — 생성 + Registered 이벤트 발행 (이메일 인증 메일 트리거)
     */
    public function register(array $validated): User
    {
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'member',
        ]);

        event(new Registered($user));

        return $user;
    }

    /**
     * 프로필 수정 — 이메일 변경 시 인증 초기화
     */
    public function updateProfile(User $user, array $validated): void
    {
        $user->fill($validated);

        // 이메일이 바뀌면 재인증 필요
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }

    /**
     * 회원 탈퇴 — 세션 무효화 전 삭제
     */
    public function deleteAccount(User $user): void
    {
        $user->delete();
    }

    /**
     * 마지막 로그인 시각 갱신
     */
    public function recordLogin(User $user): void
    {
        $user->update(['last_login_at' => now()]);
    }
}
