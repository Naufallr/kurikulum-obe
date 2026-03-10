<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan CPL-BK-MK - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blade-animations.css') }}">
    <style>
        .modal-content-large {
            width: 95%;
            max-width: 95vw;
        }

        .modal-content-large form {
            padding: 20px 24px 24px;
        }

        .modal-table-wrapper {
            max-height: 65vh;
            overflow: auto;
        }

        .modal-content-large .data-table th,
        .modal-content-large .data-table td {
            white-space: nowrap;
        }

        .modal-content-large select {
            min-width: 180px;
            padding: 6px 8px;
        }

        @media (max-width: 768px) {
            .modal-content-large {
                width: 98%;
            }
        }
    </style>
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
                <a href="{{ route('dashboard') }}" class="nav-item">Profile Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-lulusan-prodi.index') }}" class="nav-item">Capaian Pembelajaran Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('bahan-kajian.index') }}" class="nav-item">Bahan Kajian</a>
                <div class="nav-divider"></div>
                <a href="{{ route('mata-kuliah.index') }}" class="nav-item">Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-pembelajaran-mata-kuliah.index') }}" class="nav-item">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item">Sub-CPMK</a>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible" onclick="toggleSubmenu(event)">
                    <span>Penilaian</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('tahap-mekanisme-penilaian.index') }}" class="nav-item submenu-item">Teknik Penilaian</a>
                    <a href="{{ route('bobot-penilaian.index') }}" class="nav-item submenu-item">Bobot Penilaian</a>
                </div>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible" onclick="toggleSubmenu(event)">
                    <span>Pemetaan</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu active">
                    <a href="{{ route('pemetaan-cpl-pl.index') }}" class="nav-item submenu-item">CPL-PL</a>
                    <a href="{{ route('pemetaan-cpl-bk.index') }}" class="nav-item submenu-item">CPL-BK</a>
                    <a href="{{ route('pemetaan-bk-mk.index') }}" class="nav-item submenu-item">BK-MK</a>
                    <a href="{{ route('pemetaan-cpl-mk.index') }}" class="nav-item submenu-item">CPL-MK</a>
                    <a href="{{ route('pemetaan-cpl-bk-mk.index') }}" class="nav-item submenu-item active">CPL-BK-MK</a>
                    <a href="{{ route('organisasi-mata-kuliah.index') }}" class="nav-item submenu-item">Organisasi Mata Kuliah (OMK)</a>
                    <a href="{{ route('peta-pemenuhan-cpl.index') }}" class="nav-item submenu-item">Peta Pemenuhan CPL</a>
                    <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item submenu-item">Pemetaan CPL-CPMK-MK</a>
                    <a href="{{ route('sub-cpmk.index') }}" class="nav-item submenu-item">Pemetaan MK-CPMK-SubCPMK</a>
                </div>
            </nav>

            </aside>

        <main class="main-content">
            <h1 class="content-title">PEMETAAN CPL - BK - MK</h1>

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

            <div class="action-buttons">
                <button class="btn-action btn-tambah" type="button" onclick="openModal()">
                    <i class="fas fa-plus"></i> Atur Pemetaan
                </button>
            </div>

            @php
                $cplMap = $cpls->keyBy('id');
                $bkMap = $bks->keyBy('id');
                $mkMap = $mks->keyBy('id');
                $summaryRows = [];

                foreach ($selectedMk as $cplId => $bkItems) {
                    foreach ($bkItems as $bkId => $mkId) {
                        if (empty($mkId)) {
                            continue;
                        }

                        $summaryRows[] = [
                            'id_cpl' => $cplId,
                            'desk_cpl' => optional($cplMap->get($cplId))->desk,
                            'id_bk' => $bkId,
                            'bahan_kajian' => optional($bkMap->get($bkId))->bahan_kajian,
                            'id_mk' => $mkId,
                            'nama_mk' => optional($mkMap->get($mkId))->nama_mk,
                        ];
                    }
                }

                usort($summaryRows, function ($a, $b) {
                    return [$a['id_cpl'], $a['id_bk'], $a['id_mk']] <=> [$b['id_cpl'], $b['id_bk'], $b['id_mk']];
                });
            @endphp

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">NO</th>
                            <th>KODE CPL</th>
                            <th>KODE BK</th>
                            <th>KODE MK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summaryRows as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span>{{ $row['id_cpl'] }}</span>
                                    @if(!empty($row['desk_cpl']))
                                        <i class="fas fa-eye" title="{{ $row['desk_cpl'] }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $row['id_bk'] }}</span>
                                    @if(!empty($row['bahan_kajian']))
                                        <i class="fas fa-eye" title="{{ $row['bahan_kajian'] }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ $row['id_mk'] }}</span>
                                    @if(!empty($row['nama_mk']))
                                        <i class="fas fa-eye" title="{{ $row['nama_mk'] }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">
                                    <em>Tidak ada data pemetaan</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modalForm" class="modal">
        <div class="modal-content modal-content-large">
            <div class="modal-header">
                <h2>Atur Pemetaan CPL-BK-MK</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="{{ route('pemetaan-cpl-bk-mk.store') }}">
                @csrf
                <div class="modal-table-wrapper">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" width="60">NO</th>
                                    <th rowspan="2" width="120">KODE CPL</th>
                                    <th colspan="{{ max(1, $bks->count()) }}" style="text-align: center;">BAHAN KAJIAN (BK)</th>
                                </tr>
                                <tr>
                                    @forelse($bks as $bk)
                                        <th style="text-align: center;">
                                            <span>{{ $bk->id }}</span>
                                            <i class="fas fa-eye" title="{{ $bk->bahan_kajian }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                        </th>
                                    @empty
                                        <th style="text-align: center;">-</th>
                                    @endforelse
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cpls as $index => $cpl)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span>{{ $cpl->id }}</span>
                                            <i class="fas fa-eye" title="{{ $cpl->desk }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                        </td>
                                        @forelse($bks as $bk)
                                            <td style="vertical-align: top;">
                                                @php
                                                    $selectedMkId = $selectedMk[$cpl->id][$bk->id] ?? '';
                                                @endphp
                                                <select name="mapping[{{ $cpl->id }}][{{ $bk->id }}]">
                                                    <option value="">- Pilih MK -</option>
                                                    @foreach($mks as $mk)
                                                        <option value="{{ $mk->id }}" {{ $selectedMkId === $mk->id ? 'selected' : '' }}>
                                                            {{ $mk->id }} - {{ $mk->nama_mk }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @empty
                                            <td style="text-align: center;">
                                                <em>Tidak ada BK</em>
                                            </td>
                                        @endforelse
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 2 + max(1, $bks->count()) }}" style="text-align: center; padding: 20px;">
                                            <em>Tidak ada data CPL</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan Pemetaan</button>
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
        const modal = document.getElementById('modalForm');

        function openModal() {
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
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



