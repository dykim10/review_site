<div class="p-card">
    <div class="p-card-head">
        <p class="p-card-title">비밀번호 변경</p>
        <p class="p-card-desc">
            보안을 위해 등록된 이메일로 재설정 링크를 보내드립니다.
            메일의 링크를 클릭한 뒤 새 비밀번호를 설정해 주세요.
        </p>
    </div>

    @error('password_reset')
        <p class="field-error" style="margin-bottom: 1rem;">{{ $message }}</p>
    @enderror

    <form method="post" action="{{ route('profile.password-reset') }}">
        @csrf

        <p class="p-card-desc" style="margin-bottom: 1rem;">
            발송 대상: <strong>{{ auth()->user()->email }}</strong>
        </p>

        <div class="btn-row">
            <button type="submit" class="btn-submit">재설정 링크 받기</button>

            @if (session('status') === 'password-reset-link-sent')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 5000)"
                    class="saved-msg"
                >이메일을 발송했습니다. 받은편지함을 확인해 주세요.</p>
            @endif
        </div>
    </form>
</div>
