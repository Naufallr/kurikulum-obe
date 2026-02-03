<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan Sub-CPMK - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <button class="btn-keluar">Keluar</button>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-item">Profile</a>
                <div class="nav-divider"></div>
                <a href="{{ route('dashboard') }}" class="nav-item">Profile Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('capaian-lulusan-prodi.index') }}" class="nav-item">Capaian Pembelajaran Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{ route('bahan-kajian.index') }}" class="nav-item">Bahan Kajian</a>
                <div class="nav-divider"></div>
                <a href="{{route('mata-kuliah.index')}}" class="nav-item">Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item active">Sub-CPMK</a>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible active-submenu" onclick="toggleSubmenu(event)">
                    <span>Penilaian</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu active">
                    <a href="{{route('tahap-mekanisme-penilaian.index')}}" class="nav-item submenu-item">Teknik Penilaian</a>
                    <a href="#" class="nav-item submenu-item">Bobot Penilaian</a>
                </div>
            </nav>

            <!-- User Info at Bottom -->
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
                                                <td rowspan="{{ $mkRowspan }}">{{ $mkId }}</td>
                                                @php $mkFirstRowPrinted = true; @endphp
                                            @endif

                                            @if(!$cplFirstRowPrinted)
                                                <td rowspan="{{ $cplRowspan }}">{{ $cplId }}</td>
                                                @php $cplFirstRowPrinted = true; @endphp
                                            @endif

                                            @if(!$cpmkFirstRowPrinted)
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkId }}</td>
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkFirst->desk_cpmk }}</td>
                                                @php $cpmkFirstRowPrinted = true; @endphp
                                            @endif

                                            <td>{{ $row->sub_cpmk }} : {{ $row->desk_subcpmk }}</td>
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
                    <label for="id_mk">MK <span class="required">*</span></label>
                    <select id="id_mk" name="id_mk" class="form-input-modal" required>
                        <option value="">Pilih Mata Kuliah</option>
                        @foreach($mks as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->id }} - {{ $mk->nama_mk }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpl">CPL <span class="required">*</span></label>
                    <input type="text" id="id_cpl" name="id_cpl" class="form-input-modal" readonly required>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpmk">CPMK <span class="required">*</span></label>
                    <select id="id_cpmk" name="id_cpmk" class="form-input-modal" required onchange="updateCpmkFields()">
                        <option value="">Pilih CPMK</option>
                        @foreach($cpmks as $cpmk)
                            <option value="{{ $cpmk->id_cpmk }}"
                                data-id-mk="{{ $cpmk->id_mk }}"
                                data-id-cpl="{{ $cpmk->id_cpl }}"
                                data-desk-cpmk="{{ $cpmk->desk_cpmk }}">
                                {{ $cpmk->id_cpmk }} - {{ $cpmk->id_mk }} / {{ $cpmk->id_cpl }}
                            </option>
                        @endforeach
                    </select>
                    <small style="display:block;margin-top:6px;color:#666;">Pilih CPMK untuk mengisi CPL & Deskripsi CPMK otomatis.</small>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpmk">Deskripsi CPMK <span class="required">*</span></label>
                    <textarea id="desk_cpmk" name="desk_cpmk" class="form-input-modal" rows="3" readonly required></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="sub_cpmk">Sub-CPMK <span class="required">*</span></label>
                    <input type="text" id="sub_cpmk" name="sub_cpmk" class="form-input-modal" required placeholder="Contoh: Sub-CPMK0111">
                </div>

                <div class="form-group-modal">
                    <label for="desk_subcpmk">Deskripsi Sub-CPMK <span class="required">*</span></label>
                    <textarea id="desk_subcpmk" name="desk_subcpmk" class="form-input-modal" rows="3" required></textarea>
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

        // CPMK selection -> fill CPL + deskripsi CPMK
        function updateCpmkFields() {
            const idMk = document.getElementById('id_mk').value;
            const select = document.getElementById('id_cpmk');
            const opt = select.options[select.selectedIndex];
            const idCplInput = document.getElementById('id_cpl');
            const deskCpmkInput = document.getElementById('desk_cpmk');

            if (!opt || !opt.value) {
                idCplInput.value = '';
                deskCpmkInput.value = '';
                return;
            }

            // Optional: warning jika CPMK tidak sesuai MK yang dipilih
            const optMk = opt.getAttribute('data-id-mk') || '';
            if (idMk && optMk && idMk !== optMk) {
                // tetap isi field tapi inform user
                console.warn('CPMK yang dipilih tidak sesuai dengan MK yang sedang dipilih.');
            }

            idCplInput.value = opt.getAttribute('data-id-cpl') || '';
            deskCpmkInput.value = opt.getAttribute('data-desk-cpmk') || '';
        }

        // Modal functions
        const modal = document.getElementById('modalForm');

        function openModal(mode) {
            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("pemetaan-subcpmk.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('deskSubcpmkOld').value = '';

                // enable selects
                document.getElementById('id_mk').disabled = false;
                document.getElementById('id_cpmk').disabled = false;
                document.getElementById('sub_cpmk').readOnly = false;
                document.getElementById('desk_subcpmk').readOnly = false;

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

                    document.getElementById('id_mk').value = data.id_mk;
                    document.getElementById('id_cpl').value = data.id_cpl;
                    document.getElementById('id_cpmk').value = data.id_cpmk;
                    document.getElementById('desk_cpmk').value = data.desk_cpmk;
                    document.getElementById('sub_cpmk').value = data.sub_cpmk;
                    document.getElementById('desk_subcpmk').value = data.desk_subcpmk;

                    // make MK/CPMK readonly in edit (sesuai request: edit per sub-cpmk, bukan pindah mapping)
                    document.getElementById('id_mk').disabled = true;
                    document.getElementById('id_cpmk').disabled = true;
                    document.getElementById('sub_cpmk').readOnly = false;
                    document.getElementById('desk_subcpmk').readOnly = false;

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
</body>
</html>
