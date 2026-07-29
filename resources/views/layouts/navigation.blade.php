<header class="h-bar" x-data="{ open: false }">
    <a href="{{ route('home') }}" class="h-logo">PAC<span>-RUN</span></a>

    {{-- Desktop Nav --}}
    <nav class="h-nav">
        <a href="{{ route('races.index') }}"
           class="h-link {{ request()->routeIs('races.*') ? 'h-link-active' : '' }}">대회</a>

        @auth
            <a href="{{ route('dashboard') }}"
               class="h-link {{ request()->routeIs('dashboard') ? 'h-link-active' : '' }}">대시보드</a>

            <a href="{{ route('profile.edit') }}"
               class="h-link {{ request()->routeIs('profile.*') ? 'h-link-active' : '' }}">프로필</a>

            @if(in_array(auth()->user()->role ?? '', ['super_admin', 'region_admin']))
                <a href="{{ route('races-admin.races.index') }}" class="h-link h-link-admin">관리자</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="h-btn h-btn-ghost">로그아웃</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="h-btn h-btn-outline">로그인</a>
            <a href="{{ route('register') }}" class="h-btn h-btn-fill">회원가입</a>
        @endauth
    </nav>
</header>
