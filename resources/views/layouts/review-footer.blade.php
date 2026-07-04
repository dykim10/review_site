<footer class="rv-footer">
    <div class="rv-footer__inner">
        <div>
            <div class="rv-footer__logo"><em>PAC-RUN</em> REVIEW</div>
            <p class="rv-footer__desc">
                마라톤·러닝 대회 리뷰 아카이브.<br>
                실제 참가자의 경험이 쌓이는 곳.
            </p>
        </div>
        <nav class="rv-footer__nav" aria-label="푸터 링크">
            <a href="{{ route('races.index') }}">대회 목록</a>
            @auth
                <a href="{{ route('dashboard') }}">대시보드</a>
            @endauth
            <a href="{{ route('privacy') }}">개인정보처리방침</a>
        </nav>
    </div>
    <div class="rv-footer__copy">
        © {{ date('Y') }} PAC-RUN. 대회 데이터는 참고용이며 공식 정보와 다를 수 있습니다.
    </div>
</footer>
