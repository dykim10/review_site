<x-guest-layout>
    <style>
        .auth-heading { margin-bottom: 1.5rem; }
        .auth-heading h1 { font-family: 'Bebas Neue', sans-serif; font-size: 1.8rem; letter-spacing: 0.06em; color: var(--text); margin-bottom: 0.2rem; }
        .auth-heading p { font-size: 0.78rem; color: var(--muted); }

        .verify-icon { text-align: center; margin-bottom: 1.5rem; }
        .verify-icon svg { width: 48px; height: 48px; color: var(--accent); }

        .verify-msg {
            margin-bottom: 1.5rem;
            padding: 1rem 1.1rem;
            background: rgba(255,107,53,0.05);
            border: 1px solid rgba(255,107,53,0.18);
            border-radius: 9px;
        }
        .verify-msg-ko {
            font-size: 0.88rem; color: var(--text); line-height: 1.7;
            margin-bottom: 0.65rem;
        }
        .verify-msg-en {
            font-size: 0.77rem; color: var(--muted); line-height: 1.6;
            border-top: 1px solid var(--border); padding-top: 0.6rem;
        }

        .verify-sent {
            padding: 0.7rem 1rem;
            background: rgba(74,222,128,0.07);
            border: 1px solid rgba(74,222,128,0.22);
            border-radius: 8px;
            margin-bottom: 1.25rem;
        }
        .verify-sent-ko { font-size: 0.82rem; color: #4ADE80; }
        .verify-sent-en { font-size: 0.75rem; color: #86efac; margin-top: 0.2rem; }

        .verify-actions {
            display: flex; align-items: center;
            justify-content: space-between;
            gap: 1rem; flex-wrap: wrap;
        }
        .auth-submit {
            padding: 0.6rem 1.5rem; background: var(--accent); color: #fff;
            border: none; border-radius: 7px; font-size: 0.85rem; font-weight: 600;
            font-family: 'Noto Sans KR', sans-serif; cursor: pointer;
            transition: background 0.15s;
        }
        .auth-submit:hover { background: var(--accent-d); }
        .auth-link {
            font-size: 0.78rem; color: var(--muted);
            background: none; border: none; cursor: pointer;
            font-family: 'Noto Sans KR', sans-serif; transition: color 0.15s;
            text-decoration: underline; text-underline-offset: 3px;
        }
        .auth-link:hover { color: var(--accent); }
    </style>

    <div class="auth-heading">
        <h1>이메일 인증</h1>
        <p>Email Verification</p>
    </div>

    <div class="verify-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
             stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M2 7l10 7 10-7"/>
        </svg>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="verify-sent">
            <p class="verify-sent-ko">인증 메일이 재발송되었습니다. 받은 편지함을 확인해주세요.</p>
            <p class="verify-sent-en">A new verification link has been sent. Please check your inbox.</p>
        </div>
    @endif

    <div class="verify-msg">
        <p class="verify-msg-ko">
            가입해 주셔서 감사합니다!<br>
            서비스 이용 전, 가입 시 입력한 이메일 주소로 발송된 <strong>인증 링크</strong>를 클릭해 주세요.
            메일을 받지 못하셨다면 아래에서 재발송할 수 있습니다.
        </p>
        <p class="verify-msg-en">
            Thanks for signing up! Before getting started, please verify your email address
            by clicking the link we just sent you. If you didn't receive the email,
            click the button below to resend it.
        </p>
    </div>

    <div class="verify-actions">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-submit">
                인증 메일 재발송 &middot; Resend Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-link">
                로그아웃 &middot; Log out
            </button>
        </form>
    </div>
</x-guest-layout>
