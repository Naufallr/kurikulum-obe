<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan CPL-BK - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blade-animations.css') }}">
</head>
<body>
    <header class="top-header">
        <div class="header-left">
            <img src="{{ asset('storage/logo ukri.png') }}" alt="Logo UKRI" class="header-logo">
            <div class="header-text">
                <span>UNIVERSITAS KEBANGSAAN REPUBLIK INDONESIA</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <button type="button" class="user-info user-info--header user-dropdown-toggle">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                    </div>
                    <div class="user-chevron">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="user-dropdown-menu">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="user-dropdown-item">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                @include('partials.sidebar-nav')
            </nav>

            </aside>

        <main class="main-content">
            <h1 class="content-title">PEMETAAN CPL - BK</h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pemetaan-cpl-bk.store') }}">
                @csrf
                <div class="action-buttons">
                    <button class="btn-action btn-tambah" type="submit">
                        <i class="fas fa-save"></i> Simpan Pemetaan
                    </button>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th rowspan="2" width="60">NO</th>
                                <th rowspan="2" width="120">KODE BK</th>
                                <th colspan="{{ max(1, $cpls->count()) }}" style="text-align: center;">CAPAIAN PEMBELAJARAN LULUSAN (CPL)</th>
                            </tr>
                            <tr>
                                @forelse($cpls as $cpl)
                                    <th style="text-align: center;">
                                        <span>{{ $cpl->id }}</span>
                                        <i class="fas fa-eye" title="{{ $cpl->desk }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                    </th>
                                @empty
                                    <th style="text-align: center;">-</th>
                                @endforelse
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bks as $index => $bk)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span>{{ $bk->id }}</span>
                                        <i class="fas fa-eye" title="{{ $bk->bahan_kajian }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                    </td>
                                    @forelse($cpls as $cpl)
                                        @php
                                            $isChecked = !empty($mapped[$bk->id][$cpl->id]);
                                        @endphp
                                        <td style="text-align: center;">
                                            <input
                                                type="checkbox"
                                                name="mapping[{{ $bk->id }}][{{ $cpl->id }}]"
                                                value="1"
                                                {{ $isChecked ? 'checked' : '' }}
                                            >
                                        </td>
                                    @empty
                                        <td style="text-align: center;">
                                            <em>Tidak ada CPL</em>
                                        </td>
                                    @endforelse
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 2 + max(1, $cpls->count()) }}" style="text-align: center; padding: 20px;">
                                        <em>Tidak ada data BK</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            @include('partials.page-footer')
        </main>
    </div>

    <script>
        function toggleSubmenu(event) {
            event.preventDefault();
            const item = event.currentTarget;
            const chevron = item.querySelector('i');

            let nextElement = item.nextElementSibling;
            while (nextElement && !nextElement.classList.contains('submenu')) {
                if (nextElement.classList.contains('nav-divider')) {
                    nextElement = nextElement.nextElementSibling;
                } else {
                    break;
                }
            }

            const submenu = nextElement;

            if (submenu && submenu.classList.contains('submenu')) {
                const isActive = submenu.classList.contains('active');

                if (isActive) {
                    submenu.classList.remove('active');
                    item.classList.remove('active-submenu');
                    chevron.classList.remove('fa-chevron-down');
                    chevron.classList.add('fa-chevron-right');
                } else {
                    submenu.classList.add('active');
                    item.classList.add('active-submenu');
                    chevron.classList.remove('fa-chevron-right');
                    chevron.classList.add('fa-chevron-down');
                }
            }
        }
    </script>
    <script>
        document.addEventListener('click', function (event) {
            const dropdown = event.target.closest('.user-dropdown');
            document.querySelectorAll('.user-dropdown.is-open').forEach(function (item) {
                if (item !== dropdown) {
                    item.classList.remove('is-open');
                }
            });

            if (!dropdown) {
                return;
            }

            const toggle = event.target.closest('.user-dropdown-toggle');
            if (toggle) {
                event.preventDefault();
                dropdown.classList.toggle('is-open');
            }
        });
    </script>
    <script src="{{ asset('js/blade-animations.js') }}" defer></script>
</body>
</html>







