<header class="sticky top-0 z-50 bg-white border-b border-rv-line" x-data="{ mobileOpen: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

        {{-- 로고 --}}
        <a href="{{ route('home') }}"
           class="font-archivo font-bold text-xl tracking-tight text-rv-pink shrink-0">
            PAC<span class="text-rv-ink">-RUN</span>
        </a>

        {{-- 데스크톱 네비 --}}
        <nav class="hidden md:flex items-center gap-7">
            <a href="{{ route('races.index') }}"
               class="text-xs font-archivo font-bold uppercase tracking-wider pb-1 border-b-2 transition-colors
                      {{ request()->routeIs('races.*') ? 'text-rv-ink border-rv-pink' : 'text-rv-ink2 border-transparent hover:text-rv-ink' }}">
                대회 목록
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="text-xs font-archivo font-bold uppercase tracking-wider pb-1 border-b-2 transition-colors
                          {{ request()->routeIs('dashboard') ? 'text-rv-ink border-rv-pink' : 'text-rv-ink2 border-transparent hover:text-rv-ink' }}">
                    대시보드
                </a>
            @endauth

            @if(auth()->check() && in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                <a href="{{ route('admin.races.index') }}"
                   class="text-xs font-archivo font-bold uppercase tracking-wider text-rv-pink hover:text-rv-pink/80 transition-colors">
                    관리자
                </a>
            @endif
        </nav>

        {{-- 데스크톱 사용자 영역 --}}
        <div class="hidden md:flex items-center gap-2">
            @auth
                <a href="{{ route('profile.edit') }}"
                   class="text-sm text-rv-ink2 hover:text-rv-ink transition-colors mr-1">
                    {{ auth()->user()->nickname ?? auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-1.5 text-sm font-medium border-2 border-rv-line text-rv-ink2 rounded-full hover:border-rv-ink2 hover:text-rv-ink transition-colors">
                        로그아웃
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-1.5 text-sm font-semibold border-2 border-rv-pink text-rv-pink rounded-full hover:bg-rv-pink hover:text-white transition-colors">
                    로그인
                </a>
                <a href="{{ route('register') }}"
                   class="px-4 py-1.5 text-sm bg-rv-pink text-white rounded-full hover:bg-rv-pink/90 transition-colors font-semibold shadow-sm shadow-rv-pink/30">
                    회원가입
                </a>
            @endauth
        </div>

        {{-- 모바일 햄버거 --}}
        <button type="button"
                class="md:hidden p-2 text-rv-ink2 hover:text-rv-ink"
                @click="mobileOpen = !mobileOpen"
                aria-label="메뉴">
            <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- 모바일 드로어 --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-white border-t border-rv-line px-4 py-4 space-y-3">

        <a href="{{ route('races.index') }}"
           class="block text-sm font-medium text-rv-ink py-2 border-b border-rv-line/50">
            대회 목록
        </a>

        @auth
            <a href="{{ route('dashboard') }}"
               class="block text-sm font-medium text-rv-ink2 py-2 border-b border-rv-line/50">
                대시보드
            </a>
            <a href="{{ route('profile.edit') }}"
               class="block text-sm font-medium text-rv-ink2 py-2 border-b border-rv-line/50">
                프로필
            </a>
            @if(in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                <a href="{{ route('admin.races.index') }}"
                   class="block text-sm font-medium text-rv-pink py-2 border-b border-rv-line/50">
                    관리자
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="pt-1">
                @csrf
                <button type="submit"
                        class="w-full py-2 text-sm border border-rv-line text-rv-ink2 rounded-full">
                    로그아웃
                </button>
            </form>
        @else
            <div class="flex gap-2 pt-1">
                <a href="{{ route('login') }}"
                   class="flex-1 py-2 text-sm text-center font-semibold border-2 border-rv-pink text-rv-pink rounded-full">
                    로그인
                </a>
                <a href="{{ route('register') }}"
                   class="flex-1 py-2 text-sm text-center bg-rv-pink text-white rounded-full font-semibold">
                    회원가입
                </a>
            </div>
        @endauth
    </div>
</header>
