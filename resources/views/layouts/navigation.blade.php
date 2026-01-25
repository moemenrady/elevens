<nav class="main-navbar">
    <a href="{{ route('main.create') }}" class="nav-link {{ Request::is('/') || Request::is('main') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i>
        <span>الرئيسية</span>
    </a> <a href="{{ route('products.index') }}" class="nav-link {{ Request::is('products') ? 'active' : '' }}">
        <i class="fa-solid fa-box"></i>
        <span>المخزن</span>
    </a>


    {{-- @if (Auth::user()->role === 'admin')


        <a href="{{ route('managment.create') }}" 
           class="nav-link {{ Request::is('managment') ? 'active' : '' }}">
            <i class="fa-solid fa-gear"></i>
            <span>الإدارة</span>
        </a>
    @endif --}}
</nav>
