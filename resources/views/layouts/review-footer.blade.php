<footer class="bg-rv-ink text-rv-ink3 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

            {{-- 로고 + 설명 --}}
            <div>
                <div class="font-archivo font-bold text-lg text-white tracking-tight mb-1">
                    PAC-RUN REVIEW
                </div>
                <p class="text-sm leading-relaxed max-w-xs">
                    마라톤·러닝 대회 리뷰 아카이브.<br>
                    실제 참가자의 경험이 쌓이는 곳.
                </p>
            </div>

            {{-- 링크 --}}
            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <a href="{{ route('races.index') }}" class="hover:text-white transition-colors">대회 목록</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">대시보드</a>
                @endauth
                <a href="#" class="hover:text-white transition-colors">개인정보처리방침</a>
            </nav>
        </div>

        <div class="border-t border-white/10 mt-8 pt-6 text-xs">
            © {{ date('Y') }} PAC-RUN. 대회 데이터는 참고용이며 공식 정보와 다를 수 있습니다.
        </div>
    </div>
</footer>
