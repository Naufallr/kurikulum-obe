@if(auth()->check() && auth()->user()->hasRole('admin'))
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
    <div class="nav-divider"></div>
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manajemen User</a>
    <div class="nav-divider"></div>
@endif
