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
            'name'       => $name,
            'email'      => $email,
            'email_hash' => $this->crypto->hashEmail($email),
            'email_enc'  => $this->crypto->encrypt($email),
            'name_enc'   => $this->crypto->encrypt($name),
            'password'   => $validated['password'], // User 모델 cast('hashed')가 자동 해싱
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
        $user->fill($validated);

        // 이메일이 바뀌면 재인증 + 암호화 컬럼 갱신
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->email_hash = $this->crypto->hashEmail($user->email);
            $user->email_enc  = $this->crypto->encrypt($user->email);
        }

        if ($user->isDirty('name')) {
            $user->name_enc = $this->crypto->encrypt($user->name);
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
