<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tahap Mekanisme Penilaian - Kurikulum UKRI</title>
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
                <a href="{{route('dashboard')}}" class="nav-item">Profile Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{route('capaian-lulusan-prodi.index')}}" class="nav-item">Capaian Pembelajaran Lulusan</a>
                <div class="nav-divider"></div>
                <a href="{{route('bahan-kajian.index')}}" class="nav-item">Bahan Kajian</a>
                <div class="nav-divider"></div>
                <a href="{{route('mata-kuliah.index')}}" class="nav-item">Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{route('pemetaan-cpl-cpmk-mk.index')}}" class="nav-item">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{route('pemetaan-subcpmk.index')}}" class="nav-item">Sub-CPMK</a>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible active-submenu" onclick="toggleSubmenu(event)">
                    <span>Penilaian</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu active">
                    <a href="{{route('tahap-mekanisme-penilaian.index')}}" class="nav-item submenu-item active">Teknik Penilaian</a>
                    <a href="{{route('bobot-penilaian.index')}}" class="nav-item submenu-item">Bobot Penilaian</a>
                </div>
                <div class="nav-divider"></div>
                <a href="#" class="nav-item collapsible" onclick="toggleSubmenu(event)">
                    <span>Pemetaan</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div class="submenu">
                    <a href="{{ route('pemetaan-cpl-pl.index') }}" class="nav-item submenu-item">CPL-PL</a>
                    <a href="{{ route('pemetaan-cpl-bk.index') }}" class="nav-item submenu-item">CPL-BK</a>
                    <a href="{{ route('pemetaan-bk-mk.index') }}" class="nav-item submenu-item">BK-MK</a>
                    <a href="{{ route('pemetaan-cpl-mk.index') }}" class="nav-item submenu-item">CPL-MK</a>
                    <a href="{{ route('pemetaan-cpl-bk-mk.index') }}" class="nav-item submenu-item">CPL-BK-MK</a>
                    <a href="{{ route('organisasi-mata-kuliah.index') }}" class="nav-item submenu-item">Organisasi Mata Kuliah (OMK)</a>
                    <a href="{{ route('peta-pemenuhan-cpl.index') }}" class="nav-item submenu-item">Peta Pemenuhan CPL</a>
                    <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item submenu-item">Pemetaan CPL-CPMK-MK</a>
                    <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item submenu-item">Pemetaan MK-CPMK-SubCPMK</a>
                </div>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <h1 class="content-title">TAHAP MEKANISME PENILAIAN</h1>

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
                            <th>CPL</th>
                            <th>MK</th>
                            <th>CPMK</th>
                            <th>Tahap Penilaian</th>
                            <th>Teknik Penilaian</th>
                            <th>Instrumen</th>
                            <th>Kriteria</th>
                            <th>Bobot</th>
                            <th>Bobot MK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByCpl = $data->groupBy('id_cpl');
                        @endphp

                        @forelse($groupedByCpl as $cplId => $cplItems)
                            @php
                                $groupedByMk = $cplItems->groupBy('id_mk');
                                $cplRowspan = $cplItems->count();
                                $printedCpl = false;
                            @endphp

                            @foreach($groupedByMk as $mkId => $mkItems)
                                @php
                                    $groupedByCpmk = $mkItems->groupBy('id_cpmk');
                                    $mkRowspan = $mkItems->count();
                                    $printedMk = false;
                                @endphp

                                @foreach($groupedByCpmk as $cpmkId => $cpmkItems)
                                    @php
                                        $printedCpmk = false;
                                        $cpmkRowspan = $cpmkItems->count();
                                        $totalBobotMk = $cpmkItems->sum('bobot');
                                    @endphp

                                    @foreach($cpmkItems as $index => $row)
                                        <tr>
                                            <td>
                                                <!-- Changed the value to uniquely identify the row for single deletion -->
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row->id }}" class="row-checkbox" onchange="updateButtons()">
                                            </td>

                                            @if(!$printedCpl)
                                                <td rowspan="{{ $cplRowspan }}">{{ $cplId }}</td>
                                                @php $printedCpl = true; @endphp
                                            @endif

                                            @if(!$printedMk)
                                                <td rowspan="{{ $mkRowspan }}">{{ $mkId }}</td>
                                                @php $printedMk = true; @endphp
                                            @endif

                                            @if(!$printedCpmk)
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkId }}</td>
                                                @php $printedCpmk = true; @endphp
                                            @endif

                                            <td>{{ $row->tahap_penilaian }}</td>
                                            <td>{{ $row->teknik_penilaian }}</td>
                                            <td>{{ $row->instrumen }}</td>
                                            <td>{{ $row->kriteria }}</td>
                                            <td>{{ $row->bobot }}</td>
                                            @if($index === 0)
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $totalBobotMk }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 20px;">
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

                <div class="form-group-modal">
                    <label for="id_cpmk">CPMK <span class="required">*</span></label>
                    <select id="id_cpmk" name="id_cpmk" class="form-input-modal" required onchange="onCpmkChange()">
                        <option value="">Pilih CPMK</option>
                        @foreach($cpmks as $cpmk)
                            <option value="{{ $cpmk->id_cpmk }}"
                                data-id-cpl="{{ $cpmk->id_cpl }}"
                                data-id-mk="{{ $cpmk->id_mk }}">
                                {{ $cpmk->id_cpl }} / {{ $cpmk->id_mk }} / {{ $cpmk->id_cpmk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpl">CPL <span class="required">*</span></label>
                    <input type="text" id="id_cpl" name="id_cpl" class="form-input-modal" readonly required>
                </div>

                <div class="form-group-modal">
                    <label for="id_mk">MK <span class="required">*</span></label>
                    <input type="text" id="id_mk" name="id_mk" class="form-input-modal" readonly required>
                </div>

                <div class="form-group-modal">
                    <label for="tahap_penilaian">Tahap Penilaian <span class="required">*</span></label>
                    <input type="text" id="tahap_penilaian" name="tahap_penilaian" class="form-input-modal" required>
                </div>

                <div class="form-group-modal">
                    <label for="teknik_penilaian">Teknik Penilaian <span class="required">*</span></label>
                    <textarea id="teknik_penilaian" name="teknik_penilaian" class="form-input-modal" rows="3" required></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="instrumen">Instrumen <span class="required">*</span></label>
                    <textarea id="instrumen" name="instrumen" class="form-input-modal" rows="3" required></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="kriteria">Kriteria <span class="required">*</span></label>
                    <textarea id="kriteria" name="kriteria" class="form-input-modal" rows="3" required></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="bobot">Bobot <span class="required">*</span></label>
                    <input type="number" id="bobot" name="bobot" class="form-input-modal" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSubmenu(event) {
            event.preventDefault();
            const item = event.currentTarget;
            const chevron = item.querySelector('i');
            const submenu = item.nextElementSibling;

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

        function onCpmkChange() {
            const select = document.getElementById('id_cpmk');
            const opt = select.options[select.selectedIndex];
            const idCplInput = document.getElementById('id_cpl');
            const idMkInput = document.getElementById('id_mk');

            if (!opt || !opt.value) {
                idCplInput.value = '';
                idMkInput.value = '';
                return;
            }

            idCplInput.value = opt.getAttribute('data-id-cpl') || '';
            idMkInput.value = opt.getAttribute('data-id-mk') || '';
        }

        const modal = document.getElementById('modalForm');

        function openModal(mode) {
            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("tahap-mekanisme-penilaian.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();

                document.getElementById('id_cpmk').disabled = false;

                modal.style.display = 'block';
                return;
            }

            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length !== 1) {
                alert('Pilih satu data untuk diedit!');
                return;
            }

            const rowId = checkboxes[0].value;
            fetch(`/tahap-mekanisme-penilaian/get-data/${encodeURIComponent(rowId)}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Data';
                    document.getElementById('formData').action = `/tahap-mekanisme-penilaian/${encodeURIComponent(rowId)}`;
                    document.getElementById('formMethod').value = 'PUT';

                    document.getElementById('id_cpl').value = data.id_cpl;
                    document.getElementById('id_mk').value = data.id_mk;
                    document.getElementById('id_cpmk').value = data.id_cpmk;
                    document.getElementById('tahap_penilaian').value = data.tahap_penilaian;
                    document.getElementById('teknik_penilaian').value = data.teknik_penilaian;
                    document.getElementById('instrumen').value = data.instrumen;
                    document.getElementById('kriteria').value = data.kriteria;
                    document.getElementById('bobot').value = data.bobot;
                    //document.getElementById('bobot_mk').value = data.bobot_mk;

                    // CPMK tidak bisa diganti saat edit
                    document.getElementById('id_cpmk').disabled = true;

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

        window.onclick = function (event) {
            if (event.target == modal) {
                closeModal();
            }
        }

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

        // --- confirmDelete(): hapus satu atau banyak data lewat 1 endpoint POST delete-multiple ---
        function confirmDelete() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Pilih data yang akan dihapus!');
                return;
            }

            const message = checkboxes.length === 1
                ? 'Apakah Anda yakin ingin menghapus data ini?'
                : `Apakah Anda yakin ingin menghapus ${checkboxes.length} data?`;

            if (!confirm(message)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tahap-mekanisme-penilaian.destroy-multiple") }}';

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
        // --- END confirmDelete() ---

        // Handle PUT submit
        document.getElementById('formData').addEventListener('submit', function (e) {
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
