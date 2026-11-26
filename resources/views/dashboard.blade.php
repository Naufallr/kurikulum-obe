<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blade-animations.css') }}">
</head>
<body>
    <!-- Top Header Bar -->
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
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                @include('partials.sidebar-nav')
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content profile-page">
            @php
                $currentUser = auth()->user();
            @endphp

            <h1 class="content-title">PROFILE PROGRAM STUDI</h1>

            @php
                $programProfile = [
                    'Fakultas' => $currentUser->fakultas ?? '-',
                    'Program Studi' => $currentUser->program_studi ?? '-',
                    'Akreditasi' => $currentUser->akreditasi ?? '-',
                    'Jenjang Pendidikan' => $currentUser->jenjang_pendidikan ?? '-',
                    'Gelar Lulusan' => $currentUser->gelar_lulusan ?? '-',
                    'Visi Keilmuan Program Studi' => $currentUser->visi_keilmuan_program_studi ?? '-',
                    'Misi Program Studi' => $currentUser->misi_program_studi ?? '-',
                ];
            @endphp

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Data belum bisa disimpan.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="action-buttons">
                <button type="button" class="btn-action btn-edit" onclick="openDashboardModal()">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <tbody>
                        @foreach ($programProfile as $label => $value)
                            <tr>
                                <th style="width: 260px;">{{ $label }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.page-footer')
        </main>
    </div>

    <div id="dashboardModal" class="modal">
        <div class="modal-content modal-content--wide">
            <div class="modal-header">
                <h2>Edit Profile Program Studi</h2>
                <span class="close" onclick="closeDashboardModal()">&times;</span>
            </div>
            <form method="POST" action="{{ route('dashboard.update') }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group-modal">
                        <label for="fakultas">Fakultas</label>
                        <input
                            type="text"
                            id="fakultas"
                            name="fakultas"
                            class="form-input-modal"
                            value="{{ old('fakultas', $currentUser->fakultas) }}"
                        >
                    </div>

                    <div class="form-group-modal">
                        <label for="program_studi">Program Studi</label>
                        <input
                            type="text"
                            id="program_studi"
                            name="program_studi"
                            class="form-input-modal"
                            value="{{ old('program_studi', $currentUser->program_studi) }}"
                        >
                    </div>

                    <div class="form-group-modal">
                        <label for="akreditasi">Akreditasi</label>
                        <input
                            type="text"
                            id="akreditasi"
                            name="akreditasi"
                            class="form-input-modal"
                            value="{{ old('akreditasi', $currentUser->akreditasi) }}"
                        >
                    </div>

                    <div class="form-group-modal">
                        <label for="jenjang_pendidikan">Jenjang Pendidikan</label>
                        <input
                            type="text"
                            id="jenjang_pendidikan"
                            name="jenjang_pendidikan"
                            class="form-input-modal"
                            value="{{ old('jenjang_pendidikan', $currentUser->jenjang_pendidikan) }}"
                        >
                    </div>

                    <div class="form-group-modal form-group-modal--full">
                        <label for="gelar_lulusan">Gelar Lulusan</label>
                        <input
                            type="text"
                            id="gelar_lulusan"
                            name="gelar_lulusan"
                            class="form-input-modal"
                            value="{{ old('gelar_lulusan', $currentUser->gelar_lulusan) }}"
                        >
                    </div>

                    <div class="form-group-modal form-group-modal--full">
                        <label for="visi_keilmuan_program_studi">Visi Keilmuan Program Studi</label>
                        <textarea
                            id="visi_keilmuan_program_studi"
                            name="visi_keilmuan_program_studi"
                            class="form-input-modal"
                            rows="4"
                        >{{ old('visi_keilmuan_program_studi', $currentUser->visi_keilmuan_program_studi) }}</textarea>
                    </div>

                    <div class="form-group-modal form-group-modal--full">
                        <label for="misi_program_studi">Misi Program Studi</label>
                        <textarea
                            id="misi_program_studi"
                            name="misi_program_studi"
                            class="form-input-modal"
                            rows="5"
                        >{{ old('misi_program_studi', $currentUser->misi_program_studi) }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeDashboardModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
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
        const dashboardModal = document.getElementById('dashboardModal');

        function openDashboardModal() {
            dashboardModal.style.display = 'block';
        }

        function closeDashboardModal() {
            dashboardModal.style.display = 'none';
        }

        window.addEventListener('click', function (event) {
            if (event.target === dashboardModal) {
                closeDashboardModal();
            }
        });

        @if($errors->any())
            openDashboardModal();
        @endif
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
