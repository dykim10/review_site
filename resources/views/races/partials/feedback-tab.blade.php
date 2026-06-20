{{-- upcoming edition: 기대/개선 게시판 --}}
<div class="s-card" style="margin-bottom:1rem;">
    <div class="s-heading">기대 / 개선 의견</div>
    <p style="font-size:0.78rem;color:var(--muted);margin-bottom:0.85rem;">
        대회 전에 기대하시는 점이나 개선 희망 사항을 남겨주세요.
    </p>

    @if(($feedbacks ?? collect())->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:0.65rem;margin-bottom:1rem;">
            @foreach($feedbacks as $fb)
                <div style="padding:0.65rem 0.75rem;background:var(--bg);border:1px solid var(--border);border-radius:8px;">
                    @if($fb->category)
                        <div style="font-size:0.65rem;color:var(--accent);font-weight:600;margin-bottom:0.25rem;">
                            {{ match($fb->category) { 'course' => '코스', 'ops' => '운영', 'registration' => '접수', default => '기타' } }}
                        </div>
                    @endif
                    <div style="font-size:0.82rem;color:var(--text2);line-height:1.6;">{{ $fb->content }}</div>
                    <div style="font-size:0.68rem;color:var(--muted);margin-top:0.35rem;">{{ $fb->created_at?->format('Y.m.d') }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @auth
        <form method="POST" action="{{ route('editions.feedback.store', $edition) }}">
            @csrf
            <select name="category"
                    style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.45rem 0.65rem;font-size:0.8rem;color:var(--text);margin-bottom:0.5rem;">
                <option value="">카테고리 (선택)</option>
                <option value="course">코스</option>
                <option value="ops">운영</option>
                <option value="registration">접수</option>
                <option value="other">기타</option>
            </select>
            <textarea name="content" required rows="4" maxlength="5000" placeholder="기대하시는 점, 개선 희망 사항을 작성해주세요."
                      style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:0.55rem 0.65rem;font-size:0.82rem;color:var(--text);resize:vertical;font-family:'Pretendard',sans-serif;"></textarea>
            @error('content')<p style="font-size:0.72rem;color:#f87171;margin-top:0.35rem;">{{ $message }}</p>@enderror
            <button type="submit" class="action-btn action-primary" style="margin-top:0.65rem;font-size:0.78rem;">의견 등록</button>
        </form>
    @else
        <p style="font-size:0.78rem;color:var(--muted);">로그인 후 의견을 남길 수 있습니다.</p>
    @endauth
</div>
