<div class="p-card" x-data="">
    <div class="p-card-head">
        <p class="p-card-title">계정 삭제</p>
        <p class="p-card-desc">
            계정을 삭제하면 모든 데이터가 영구적으로 삭제됩니다. 삭제 전 보관이 필요한 정보는 미리 백업해주세요.
        </p>
    </div>

    <button type="button" class="btn-danger" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        계정 삭제
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold" style="color:#16181D;">정말 계정을 삭제하시겠습니까?</h2>

            <p class="mt-1 text-sm" style="color:#5A6170;">
                계정을 삭제하면 모든 데이터가 영구적으로 삭제됩니다. 비밀번호를 입력해 삭제를 확인해주세요.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">비밀번호</label>
                <input
                    id="password" name="password" type="password" placeholder="비밀번호"
                    class="field-input"
                    style="width: 75%;"
                >
                @error('password', 'userDeletion')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">취소</button>
                <button type="submit" class="btn-danger-solid">계정 삭제</button>
            </div>
        </form>
    </x-modal>
</div>
