<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bobot Penilaian - Kurikulum UKRI</title>
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
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="submenu active">
                    <a href="{{route('tahap-mekanisme-penilaian.index')}}" class="nav-item submenu-item">Teknik Penilaian</a>
                    <a href="{{route('bobot-penilaian.index')}}" class="nav-item submenu-item active">Bobot Penilaian</a>
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
            <h1 class="content-title">BOBOT PENILAIAN</h1>

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
                            <th>Nama MK</th>
                            <th>CPL</th>
                            <th>CPMK</th>
                            <th>MBKM</th>
                            <th>Partisipasi<br>(Kehadiran / Quiz)</th>
                            <th>Observasi<br>(Praktek / Tugas)</th>
                            <th>Unjuk Kerja<br>(Presentasi)</th>
                            <th>Tes Tulis<br>(UTS)</th>
                            <th>Tes Tulis<br>(UAS)</th>
                            <th>Tes Lisan<br>(Tugas Kelompok)</th>
                            <th>Total</th>
                            <th>Total Nilai Per MK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByMk = $data->groupBy('id_mk');
                        @endphp

                        @forelse($groupedByMk as $mkId => $mkItems)
                            @php
                                $groupedByCpl = $mkItems->groupBy('id_cpl');
                                $mkRowspan = $mkItems->count();
                                $printedMk = false;
                                $printedNamaMk = false;
                                $printedTotalNilaiMk = false;
                                $totalNilaiMk = $mkItems->first()->total_nilai_mk ?? 0;
                                $mkFirstRow = true;
                            @endphp

                            @foreach($groupedByCpl as $cplId => $cplItems)
                                @php
                                    $groupedByCpmk = $cplItems->groupBy('id_cpmk');
                                    $cplRowspan = $cplItems->count();
                                    $printedCpl = false;
                                @endphp

                                @foreach($groupedByCpmk as $cpmkId => $cpmkItems)
                                    @php
                                        $printedCpmk = false;
                                        $cpmkRowspan = $cpmkItems->count();
                                    @endphp

                                    @foreach($cpmkItems as $index => $row)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_ids[]" value="{{ $row->id }}" class="row-checkbox" onchange="updateButtons()">
                                            </td>

                                            @if(!$printedMk)
                                                <td rowspan="{{ $mkRowspan }}">{{ $mkId }}</td>
                                                @php $printedMk = true; @endphp
                                            @endif

                                            @if(!$printedNamaMk)
                                                <td rowspan="{{ $mkRowspan }}">{{ $row->nama_mk }}</td>
                                                @php $printedNamaMk = true; @endphp
                                            @endif

                                            @if(!$printedCpl)
                                                <td rowspan="{{ $cplRowspan }}">{{ $cplId }}</td>
                                                @php $printedCpl = true; @endphp
                                            @endif

                                            @if(!$printedCpmk)
                                                <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkId }}</td>
                                                @php $printedCpmk = true; @endphp
                                            @endif

                                            <td>{{ $row->mbkm ?? '-' }}</td>
                                            <td>{{ $row->partisipasi ?? '-' }}</td>
                                            <td>{{ $row->observasi ?? '-' }}</td>
                                            <td>{{ $row->unjuk_kerja ?? '-' }}</td>
                                            <td>{{ $row->tes_uts ?? '-' }}</td>
                                            <td>{{ $row->tes_uas ?? '-' }}</td>
                                            <td>{{ $row->tes_lisan ?? '-' }}</td>
                                            <td>{{ $row->total ?? '-' }}</td>
                                            @if($mkFirstRow)
                                                <td rowspan="{{ $mkRowspan }}">{{ $totalNilaiMk }}</td>
                                                @php
                                                    $printedTotalNilaiMk = true;
                                                    $mkFirstRow = false;
                                                @endphp
                                            @endif
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="14" style="text-align: center; padding: 20px;">
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
                    <label for="mbkm">MBKM</label>
                    <input type="text" id="mbkm" name="mbkm" class="form-input-modal">
                </div>

                <div class="form-group-modal">
                    <label for="partisipasi">Partisipasi (Kehadiran / Quiz)</label>
                    <input type="number" id="partisipasi" name="partisipasi" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="observasi">Observasi (Praktek / Tugas)</label>
                    <input type="number" id="observasi" name="observasi" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="unjuk_kerja">Unjuk Kerja (Presentasi)</label>
                    <input type="number" id="unjuk_kerja" name="unjuk_kerja" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="tes_uts">Tes Tulis (UTS)</label>
                    <input type="number" id="tes_uts" name="tes_uts" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="tes_uas">Tes Tulis (UAS)</label>
                    <input type="number" id="tes_uas" name="tes_uas" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="tes_lisan">Tes Lisan (Tugas Kelompok)</label>
                    <input type="number" id="tes_lisan" name="tes_lisan" class="form-input-modal" min="0" max="100" step="0.01" onchange="calculateTotal()">
                </div>

                <div class="form-group-modal">
                    <label for="total">Total</label>
                    <input type="number" id="total" name="total" class="form-input-modal" readonly>
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

        function calculateTotal() {
            const partisipasi = parseFloat(document.getElementById('partisipasi').value) || 0;
            const observasi = parseFloat(document.getElementById('observasi').value) || 0;
            const unjukKerja = parseFloat(document.getElementById('unjuk_kerja').value) || 0;
            const tesUts = parseFloat(document.getElementById('tes_uts').value) || 0;
            const tesUas = parseFloat(document.getElementById('tes_uas').value) || 0;
            const tesLisan = parseFloat(document.getElementById('tes_lisan').value) || 0;

            const total = partisipasi + observasi + unjukKerja + tesUts + tesUas + tesLisan;
            document.getElementById('total').value = total.toFixed(2);
        }

        const modal = document.getElementById('modalForm');

        function openModal(mode) {
            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("bobot-penilaian.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('total').value = '';

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
            fetch(`/bobot-penilaian/get-data/${encodeURIComponent(rowId)}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Data';
                    document.getElementById('formData').action = `/bobot-penilaian/${encodeURIComponent(rowId)}`;
                    document.getElementById('formMethod').value = 'PUT';

                    document.getElementById('id_cpl').value = data.id_cpl;
                    document.getElementById('id_mk').value = data.id_mk;
                    document.getElementById('id_cpmk').value = data.id_cpmk;
                    document.getElementById('mbkm').value = data.mbkm || '';
                    document.getElementById('partisipasi').value = data.partisipasi || '';
                    document.getElementById('observasi').value = data.observasi || '';
                    document.getElementById('unjuk_kerja').value = data.unjuk_kerja || '';
                    document.getElementById('tes_uts').value = data.tes_uts || '';
                    document.getElementById('tes_uas').value = data.tes_uas || '';
                    document.getElementById('tes_lisan').value = data.tes_lisan || '';
                    document.getElementById('total').value = data.total || '';

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
            form.action = '{{ route("bobot-penilaian.destroy-multiple") }}';

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









