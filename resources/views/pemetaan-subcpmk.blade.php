<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan Sub-CPMK - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
                <a href="{{ route('dashboard') }}" class="nav-item">Profile Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-lulusan-prodi.index') }}" class="nav-item">Capaian Pembelajaran Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('bahan-kajian.index') }}" class="nav-item">Bahan Kajian</a>
                <div class="nav-divider"></div>
                <a href="{{route('mata-kuliah.index')}}" class="nav-item">Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-pembelajaran-mata-kuliah.index') }}" class="nav-item">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('sub-cpmk.index') }}" class="nav-item">Sub-CPMK</a>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible" onclick="toggleSubmenu(event)">
                    <span>Penilaian</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{route('tahap-mekanisme-penilaian.index')}}" class="nav-item submenu-item">Teknik Penilaian</a>
                    <a href="{{route('bobot-penilaian.index')}}" class="nav-item submenu-item">Bobot Penilaian</a>
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
                    <a href="{{ route('pemetaan-cpl-bk-mk.index') }}" class="nav-item submenu-item">CPL-BK-MK</a>
                    <a href="{{ route('organisasi-mata-kuliah.index') }}" class="nav-item submenu-item">Organisasi Mata Kuliah (OMK)</a>
                    <a href="{{ route('peta-pemenuhan-cpl.index') }}" class="nav-item submenu-item">Peta Pemenuhan CPL</a>
                    <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item submenu-item">Pemetaan CPL-CPMK-MK</a>
                    <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item submenu-item active">Pemetaan MK-CPMK-SubCPMK</a>
                </div>
            </nav>
</aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <h1 class="content-title">PEMETAAN MK CPL CPMK SUB-CPMK</h1>

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
                <button class="btn-action btn-tambah" onclick="openModal('create')">
                    <i class="fas fa-plus"></i> Tambah
                </button>
                <button class="btn-action btn-edit" onclick="openModal('edit')" id="btnEdit" disabled>
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-action btn-hapus" onclick="confirmDelete()" id="btnDelete" disabled>
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>MK</th>
                            <th>CPL</th>
                            <th>CPMK</th>
                            <th>Deskripsi CPMK</th>
                            <th>Sub-CPMK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Group: MK -> CPL -> CPMK
                            $groupedByMk = $data->groupBy('id_mk');
                            $mkNameMap = $mks->pluck('nama_mk', 'id');
                            $cplDeskMap = $cpls->pluck('desk', 'id');
                        @endphp

                        @forelse($groupedByMk as $mkId => $mkItems)
                            @php
                                $groupedByCpl = $mkItems->groupBy('id_cpl');
                                $mkRowspan = $mkItems->count();
                                $mkFirstRowPrinted = false;
                            @endphp

                            @foreach($groupedByCpl as $cplId => $cplItems)
                                @php
                                    $groupedByCpmk = $cplItems->groupBy('id_cpmk');
                                    $cplRowspan = $cplItems->count();
                                    $cplFirstRowPrinted = false;
                                @endphp

                                @foreach($groupedByCpmk as $cpmkId => $cpmkItems)
                                    @php
                                        $cpmkRowspan = $cpmkItems->count();
                                        $cpmkFirst = $cpmkItems->first();
                                        $cpmkFirstRowPrinted = false;
                                    @endphp

                                    @foreach($cpmkItems as $row)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row->desk_subcpmk }}" class="row-checkbox" onchange="updateButtons()">
                                            </td>

                                            @if(!$mkFirstRowPrinted)
                                                <td rowspan="{{ $mkRowspan }}">
                                                    <span>{{ $mkId }}</span>
                                                    <i class="fas fa-eye" title="{{ $mkNameMap[$mkId] ?? '-' }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                                </td>
                                                @php $mkFirstRowPrinted = true; @endphp
                                            @endif

                                            @if(!$cplFirstRowPrinted)
                                                <td rowspan="{{ $cplRowspan }}">
                                                    <span>{{ $cplId }}</span>
                                                    <i class="fas fa-eye" title="{{ $cplDeskMap[$cplId] ?? '-' }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                                </td>
                                                @php $cplFirstRowPrinted = true; @endphp
                                            @endif

                                            @if(!$cpmkFirstRowPrinted)
                                                <td rowspan="{{ $cpmkRowspan }}">
                                                    <span>{{ $cpmkId }}</span>
                                                    <i class="fas fa-eye" title="{{ $cpmkFirst->desk_cpmk }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                                </td>
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkFirst->desk_cpmk }}</td>
                                                @php $cpmkFirstRowPrinted = true; @endphp
                                            @endif

                                            <td>
                                                <span>{{ $row->sub_cpmk }}</span>
                                                <i class="fas fa-eye" title="{{ $row->desk_subcpmk }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                                : {{ $row->desk_subcpmk }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">
                                    <em>Tidak ada data</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal Form -->
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Data</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formData" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="desk_subcpmk_old" id="deskSubcpmkOld">

                <div class="form-group-modal">
                    <label for="source_id_subcpmk">Pilih Sub-CPMK <span class="required">*</span></label>
                    <select id="source_id_subcpmk" name="source_id_subcpmk" class="form-input-modal" required onchange="applySelectedSubcpmkData()">
                        <option value="">Pilih Sub-CPMK</option>
                        @foreach($subcpmkMaster as $sub)
                            <option
                                value="{{ $sub->id_subcpmk }}"
                                data-id-cpl="{{ $sub->id_cpl }}"
                                data-id-cpmk="{{ $sub->id_cpmk }}"
                                data-desk-cpmk="{{ $sub->desk_cpmk }}"
                                data-desk-subcpmk="{{ $sub->desk_subcpmk }}">
                                {{ $sub->id_subcpmk }} - {{ \Illuminate\Support\Str::limit($sub->desk_subcpmk, 70) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpl_display">CPL</label>
                    <input type="text" id="id_cpl_display" class="form-input-modal" readonly>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpmk_display">CPMK</label>
                    <input type="text" id="id_cpmk_display" class="form-input-modal" readonly>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpmk_display">Deskripsi CPMK</label>
                    <textarea id="desk_cpmk_display" class="form-input-modal" rows="3" readonly></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="desk_subcpmk_display">Deskripsi Sub-CPMK</label>
                    <textarea id="desk_subcpmk_display" class="form-input-modal" rows="3" readonly></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="id_mk">MK <span class="required">*</span></label>
                    <select id="id_mk" name="id_mk" class="form-input-modal" required>
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach($mks as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->id }} - {{ $mk->nama_mk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle submenu (match style page lain)
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

        // Modal functions
        const modal = document.getElementById('modalForm');

        function applySelectedSubcpmkData() {
            const select = document.getElementById('source_id_subcpmk');
            const selected = select.options[select.selectedIndex];

            const idCpl = document.getElementById('id_cpl_display');
            const idCpmk = document.getElementById('id_cpmk_display');
            const deskCpmk = document.getElementById('desk_cpmk_display');
            const deskSubcpmk = document.getElementById('desk_subcpmk_display');

            if (!selected || !selected.value) {
                idCpl.value = '';
                idCpmk.value = '';
                deskCpmk.value = '';
                deskSubcpmk.value = '';
                return;
            }

            idCpl.value = selected.getAttribute('data-id-cpl') || '';
            idCpmk.value = selected.getAttribute('data-id-cpmk') || '';
            deskCpmk.value = selected.getAttribute('data-desk-cpmk') || '';
            deskSubcpmk.value = selected.getAttribute('data-desk-subcpmk') || '';
        }

        function openModal(mode) {
            const sourceSelect = document.getElementById('source_id_subcpmk');

            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("pemetaan-subcpmk.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('deskSubcpmkOld').value = '';
                document.getElementById('id_mk').disabled = false;
                applySelectedSubcpmkData();

                modal.style.display = 'block';
                return;
            }

            // edit
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length !== 1) {
                alert('Pilih satu data untuk diedit!');
                return;
            }

            const deskSub = checkboxes[0].value;
            fetch(`/pemetaan-subcpmk/${encodeURIComponent(deskSub)}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Data';
                    document.getElementById('formData').action = `/pemetaan-subcpmk/${encodeURIComponent(deskSub)}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('deskSubcpmkOld').value = data.desk_subcpmk;

                    const hasOption = Array.from(sourceSelect.options).some(opt => opt.value === data.sub_cpmk);
                    if (!hasOption && data.sub_cpmk) {
                        const fallbackOption = document.createElement('option');
                        fallbackOption.value = data.sub_cpmk;
                        fallbackOption.textContent = `${data.sub_cpmk} - ${data.desk_subcpmk}`;
                        fallbackOption.setAttribute('data-id-cpl', data.id_cpl || '');
                        fallbackOption.setAttribute('data-id-cpmk', data.id_cpmk || '');
                        fallbackOption.setAttribute('data-desk-cpmk', data.desk_cpmk || '');
                        fallbackOption.setAttribute('data-desk-subcpmk', data.desk_subcpmk || '');
                        sourceSelect.appendChild(fallbackOption);
                    }

                    sourceSelect.value = data.sub_cpmk || '';
                    applySelectedSubcpmkData();
                    document.getElementById('id_mk').value = data.id_mk;
                    document.getElementById('id_mk').disabled = false;

                    modal.style.display = 'block';
                })
                .catch(e => {
                    console.error(e);
                    alert('Gagal memuat data!');
                });
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Checkbox functions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateButtons();
        }

        function updateButtons() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const btnEdit = document.getElementById('btnEdit');
            const btnDelete = document.getElementById('btnDelete');

            if (checked.length === 1) {
                btnEdit.disabled = false;
                btnDelete.disabled = false;
            } else if (checked.length > 1) {
                btnEdit.disabled = true;
                btnDelete.disabled = false;
            } else {
                btnEdit.disabled = true;
                btnDelete.disabled = true;
            }
        }

        // Delete function (multiple)
        function confirmDelete() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Pilih data yang akan dihapus!');
                return;
            }

            if (!confirm(`Apakah Anda yakin ingin menghapus ${checkboxes.length} data?`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("pemetaan-subcpmk.destroy-multiple") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Handle PUT submission (match pattern page lain)
        document.getElementById('formData').addEventListener('submit', function(e) {
            if (document.getElementById('formMethod').value === 'PUT') {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const action = form.action;

                const putForm = document.createElement('form');
                putForm.method = 'POST';
                putForm.action = action;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                putForm.appendChild(methodInput);

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                putForm.appendChild(csrfInput);

                for (let [key, value] of formData.entries()) {
                    if (key !== '_method' && key !== '_token') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        putForm.appendChild(input);
                    }
                }

                document.body.appendChild(putForm);
                putForm.submit();
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

