<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Organisasi Mata Kuliah - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <button class="btn-keluar">Keluar</button>
            <div class="user-info user-info--header">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">Naufal Maulana</div>
                    <div class="user-id">20231320044</div>
                </div>
                <div class="user-settings">
                    <i class="fas fa-cog"></i>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item">Profile Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-lulusan-prodi.index') }}" class="nav-item">Capaian Pembelajaran Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('bahan-kajian.index') }}" class="nav-item">Bahan Kajian</a>
                <div class="nav-divider"></div>
                <a href="{{ route('mata-kuliah.index') }}" class="nav-item">Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item">Sub-CPMK</a>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible active-submenu" onclick="toggleSubmenu(event)">
                    <span>Penilaian</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('tahap-mekanisme-penilaian.index') }}" class="nav-item submenu-item">Teknik Penilaian</a>
                    <a href="{{ route('bobot-penilaian.index') }}" class="nav-item submenu-item">Bobot Penilaian</a>
                </div>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible active-submenu" onclick="toggleSubmenu(event)">
                    <span>Pemetaan</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu active">
                    <a href="{{ route('pemetaan-cpl-pl.index') }}" class="nav-item submenu-item">CPL-PL</a>
                    <a href="{{ route('pemetaan-cpl-bk.index') }}" class="nav-item submenu-item">CPL-BK</a>
                    <a href="{{ route('pemetaan-bk-mk.index') }}" class="nav-item submenu-item">BK-MK</a>
                    <a href="{{ route('pemetaan-cpl-mk.index') }}" class="nav-item submenu-item">CPL-MK</a>
                    <a href="{{ route('pemetaan-cpl-bk-mk.index') }}" class="nav-item submenu-item">CPL-BK-MK</a>
                    <a href="{{ route('organisasi-mata-kuliah.index') }}" class="nav-item submenu-item active">Organisasi Mata Kuliah (OMK)</a>
                    <a href="{{ route('peta-pemenuhan-cpl.index') }}" class="nav-item submenu-item">Peta Pemenuhan CPL</a>
                    <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item submenu-item">Pemetaan CPL-CPMK-MK</a>
                    <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item submenu-item">Pemetaan MK-CPMK-SubCPMK</a>
                </div>
            </nav>

            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">Naufal Maulana</div>
                    <div class="user-id">20231320044</div>
                </div>
                <div class="user-settings">
                    <i class="fas fa-cog"></i>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <h1 class="content-title">ORGANISASI MATA KULIAH (OMK)</h1>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">NO</th>
                            <th>KODE MK</th>
                            <th>NAMA MK</th>
                            <th>SEMESTER</th>
                            <th>SKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                <em>Belum ada data</em>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
</body>
</html>

