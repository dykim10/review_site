<div class="p-card">
    <div class="p-card-head">
        <p class="p-card-title">비밀번호 변경</p>
        <p class="p-card-desc">계정 보안을 위해 길고 예측하기 어려운 비밀번호를 사용하세요.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="field">
            <label class="field-label" for="update_password_current_password">현재 비밀번호</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="field-input" autocomplete="current-password">
            @error('current_password', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="field-label" for="update_password_password">새 비밀번호</label>
            <input id="update_password_password" name="password" type="password"
                   class="field-input" autocomplete="new-password">
            @error('password', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label class="field-label" for="update_password_password_confirmation">새 비밀번호 확인</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="field-input" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="btn-row">
            <button type="submit" class="btn-submit">저장</button>

            @if (session('status') === 'password-updated')
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
