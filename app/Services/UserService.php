<?php

namespace App\Services;

use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Auth\Events\Registered;

class UserService
{
    public function __construct(private CryptoService $crypto) {}

    /**
     * 회원가입 — 생성 + Registered 이벤트 발행 (이메일 인증 메일 트리거)
     */
    public function register(array $validated): User
    {
        $email = $validated['email'];
        $name  = $validated['name'];

        $user = User::create([
            'email_hash' => $this->crypto->hashEmail($email),
            'email_enc'  => $this->crypto->encrypt($email),
            'name_enc'   => $this->crypto->encrypt($name),
            'password'   => $validated['password'],
            'role'       => 'member',
        ]);

        event(new Registered($user));

        return $user;
    }

    /**
     * 프로필 수정 — 이메일 변경 시 인증 초기화
     */
    public function updateProfile(User $user, array $validated): void
    {
        $newEmail = $validated['email'] ?? null;
        $newName  = $validated['name']  ?? null;

        if ($newEmail && $newEmail !== $user->email) {
            $user->email_verified_at = null;
            $user->email_hash = $this->crypto->hashEmail($newEmail);
            $user->email_enc  = $this->crypto->encrypt($newEmail);
        }

        if ($newName && $newName !== $user->name) {
            $user->name_enc = $this->crypto->encrypt($newName);
        }

        $user->fill(array_diff_key($validated, array_flip(['email', 'name'])));
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
