<div class="p-card">
    <div class="p-card-head">
        <p class="p-card-title">프로필 정보</p>
        <p class="p-card-desc">계정의 이름과 이메일 주소를 변경할 수 있습니다.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="field">
            <label class="field-label" for="name">이름</label>
            <input id="name" name="name" type="text" class="field-input"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="field-label" for="email">이메일</label>
            <input id="email" name="email" type="email" class="field-input"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="verify-box">
                    이메일 인증이 완료되지 않았습니다.
                    <button form="send-verification" class="verify-link">인증 메일 다시 보내기</button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="verify-ok">인증 메일을 새로 보냈습니다.</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">저장</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="saved-msg"
                >저장되었습니다.</p>
            @endif
        </div>
    </form>
</div>
