@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin') ?? false;
    $isAuthenticated = auth()->check();

    $canManageUsers = $isAdmin;
    $canManageProfileLulusan = $isAuthenticated;
    $canManageMataKuliah = $isAuthenticated;
    $canManageBahanKajian = $isAuthenticated;

    $showPenilaian = $isAuthenticated;
    $showPemetaan = $isAuthenticated;
    $showCpl = $isAuthenticated;
    $showCpmk = $isAuthenticated;
    $showSubCpmk = $isAuthenticated;

    $penilaianActive = request()->routeIs('tahap-mekanisme-penilaian.*') || request()->routeIs('bobot-penilaian.*');
    $pemetaanActive = request()->routeIs('pemetaan-cpl-pl.*')
        || request()->routeIs('pemetaan-cpl-bk.*')
        || request()->routeIs('pemetaan-bk-mk.*')
        || request()->routeIs('pemetaan-cpl-mk.*')
        || request()->routeIs('pemetaan-cpl-bk-mk.*')
        || request()->routeIs('organisasi-mata-kuliah.*')
        || request()->routeIs('peta-pemenuhan-cpl.*')
        || request()->routeIs('pemetaan-cpl-cpmk-mk.*')
        || request()->routeIs('pemetaan-subcpmk.*');
@endphp

<a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
<div class="nav-divider"></div>

@if($canManageUsers)
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Manajemen User</a>
    <div class="nav-divider"></div>
@endif

@if($canManageProfileLulusan)
    <a href="{{ route('profile-lulusan') }}" class="nav-item {{ request()->routeIs('profile-lulusan') ? 'active' : '' }}">Profil Lulusan (PL)</a>
    <div class="nav-divider"></div>
@endif

@if($showCpl)
    <a href="{{ route('capaian-lulusan-prodi.index') }}" class="nav-item {{ request()->routeIs('capaian-lulusan-prodi.*') ? 'active' : '' }}">Capaian Pembelajaran Lulusan (CPL)</a>
    <div class="nav-divider"></div>
@endif

@if($canManageBahanKajian)
    <a href="{{ route('bahan-kajian.index') }}" class="nav-item {{ request()->routeIs('bahan-kajian.*') ? 'active' : '' }}">Bahan Kajian (BK)</a>
    <div class="nav-divider"></div>
@endif

@if($canManageMataKuliah)
    <a href="{{ route('mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}">Mata Kuliah (MK)</a>
    <div class="nav-divider"></div>
@endif

@if($showCpmk)
    <a href="{{ route('capaian-pembelajaran-mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('capaian-pembelajaran-mata-kuliah.*') ? 'active' : '' }}">Capaian Pembelajaran Mata Kuliah (CPMK)</a>
    <div class="nav-divider"></div>
@endif

@if($showSubCpmk)
    <a href="{{ route('sub-cpmk.index') }}" class="nav-item {{ request()->routeIs('sub-cpmk.*') ? 'active' : '' }}">Sub-CPMK</a>
    <div class="nav-divider"></div>
@endif

@if($showPenilaian)
    <a href="#" class="nav-item collapsible {{ $penilaianActive ? 'active-submenu' : '' }}" onclick="toggleSubmenu(event)">
        <span>Penilaian</span>
        <i class="fas {{ $penilaianActive ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
    </a>
    <div class="submenu {{ $penilaianActive ? 'active' : '' }}">
        <a href="{{ route('tahap-mekanisme-penilaian.index') }}" class="nav-item submenu-item {{ request()->routeIs('tahap-mekanisme-penilaian.*') ? 'active' : '' }}">Teknik Penilaian</a>
        <a href="{{ route('bobot-penilaian.index') }}" class="nav-item submenu-item {{ request()->routeIs('bobot-penilaian.*') ? 'active' : '' }}">Bobot Penilaian</a>
    </div>
    <div class="nav-divider"></div>
@endif

@if($showPemetaan)
    <a href="#" class="nav-item collapsible {{ $pemetaanActive ? 'active-submenu' : '' }}" onclick="toggleSubmenu(event)">
        <span>Pemetaan</span>
        <i class="fas {{ $pemetaanActive ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
    </a>
    <div class="submenu {{ $pemetaanActive ? 'active' : '' }}">
        <a href="{{ route('pemetaan-cpl-pl.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-cpl-pl.*') ? 'active' : '' }}">CPL-PL</a>
        <a href="{{ route('pemetaan-cpl-bk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-cpl-bk.*') ? 'active' : '' }}">CPL-BK</a>
        <a href="{{ route('pemetaan-bk-mk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-bk-mk.*') ? 'active' : '' }}">BK-MK</a>
        <a href="{{ route('pemetaan-cpl-mk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-cpl-mk.*') ? 'active' : '' }}">CPL-MK</a>
        <a href="{{ route('pemetaan-cpl-bk-mk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-cpl-bk-mk.*') ? 'active' : '' }}">CPL-BK-MK</a>
        <a href="{{ route('organisasi-mata-kuliah.index') }}" class="nav-item submenu-item {{ request()->routeIs('organisasi-mata-kuliah.*') ? 'active' : '' }}">Organisasi Mata Kuliah (OMK)</a>
        <a href="{{ route('peta-pemenuhan-cpl.index') }}" class="nav-item submenu-item {{ request()->routeIs('peta-pemenuhan-cpl.*') ? 'active' : '' }}">Peta Pemenuhan CPL</a>
        <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-cpl-cpmk-mk.*') ? 'active' : '' }}">Pemetaan CPL-CPMK-MK</a>
        <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item submenu-item {{ request()->routeIs('pemetaan-subcpmk.*') ? 'active' : '' }}">Pemetaan MK-CPMK-SubCPMK</a>
    </div>
@endif
