<header class="rv-header">
    <div class="rv-header__inner">

        <a href="{{ route('home') }}" class="rv-logo">
            PAC<span>-RUN</span>
        </a>

        <nav class="rv-nav rv-nav--desktop" aria-label="주요 메뉴">
            <a href="{{ route('races.index') }}"
               class="rv-nav-link {{ request()->routeIs('races.*') ? 'is-active' : '' }}">
                대회 목록
            </a>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="rv-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    대시보드
                </a>
            @endauth
            @if(auth()->check() && in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                <a href="{{ route('races-admin.races.index') }}" class="rv-nav-link rv-nav-link--admin">
                    관리자
                </a>
            @endif
        </nav>

        <div class="rv-header__actions rv-header__actions--desktop">
            @auth
                <a href="{{ route('profile.edit') }}" class="rv-user-name">
                    {{ auth()->user()->nickname ?? auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rv-btn rv-btn--ghost">로그아웃</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rv-btn rv-btn--outline">로그인</a>
                <a href="{{ route('register') }}" class="rv-btn rv-btn--primary">회원가입</a>
            @endauth
        </div>

        <details class="rv-mobile-menu">
            <summary aria-label="메뉴 열기">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </summary>
            <div class="rv-mobile-panel">
                <a href="{{ route('races.index') }}"
                   class="rv-nav-link {{ request()->routeIs('races.*') ? 'is-active' : '' }}">
                    대회 목록
                </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rv-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        대시보드
                    </a>
                    <a href="{{ route('profile.edit') }}" class="rv-nav-link">프로필</a>
                    @if(in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                        <a href="{{ route('races-admin.races.index') }}" class="rv-nav-link rv-nav-link--admin">관리자</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rv-btn rv-btn--ghost">로그아웃</button>
                    </form>
                @else
                    <div class="rv-mobile-panel__auth">
                        <a href="{{ route('login') }}" class="rv-btn rv-btn--outline">로그인</a>
                        <a href="{{ route('register') }}" class="rv-btn rv-btn--primary">회원가입</a>
                    </div>
                @endauth
            </div>
        </details>

    </div>
</header>
