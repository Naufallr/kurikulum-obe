<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan CPMK - Kurikulum UKRI</title>
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
                <a href="{{ route('pemetaan-cpl-cpmk-mk.index') }}" class="nav-item active">Capaian Pembelajaran Mata Kuliah</a>
                <div class="nav-divider"></div>
                <a href="{{ route('pemetaan-subcpmk.index') }}" class="nav-item">Sub-CPMK</a>
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
            <h1 class="content-title">PEMETAAN CPL CPMK MK</h1>

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
                            <th>NO</th>
                            <th>CPL</th>
                            <th>DESKRIPSI CPL</th>
                            <th>KODE CPMK</th>
                            <th>CPMK</th>
                            <th>MK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedData = $pemetaan->groupBy('id_cpl');
                            $rowNumber = 1;
                        @endphp
                        @forelse($groupedData as $cplId => $cpmkList)
                            @php
                                $firstItem = $cpmkList->first();
                                $rowCount = $cpmkList->count();
                            @endphp
                            @foreach($cpmkList as $index => $item)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" value="{{ $item->desk_cpmk }}" class="row-checkbox" onchange="updateButtons()">
                                    </td>
                                    @if($index === 0)
                                        <td rowspan="{{ $rowCount }}">{{ $rowNumber }}</td>
                                        <td rowspan="{{ $rowCount }}">{{ $item->id_cpl }}</td>
                                        <td rowspan="{{ $rowCount }}">{{ $item->desk_cpl }}</td>
                                    @endif
                                    <td>{{ $item->id_cpmk }}</td>
                                    <td>{{ $item->desk_cpmk }}</td>
                                    <td>{{ $item->id_mk }}</td>
                                </tr>
                            @endforeach
                            @php $rowNumber++; @endphp
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">
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
                <input type="hidden" name="desk_cpmk_old" id="formDeskCpmkOld">
                <input type="hidden" name="id_cpl" id="id_cpl_hidden">

                <div class="form-group-modal">
                    <label for="id_cpl">CPL <span class="required">*</span></label>
                    <select id="id_cpl" name="id_cpl" class="form-input-modal" required onchange="updateDeskCpl()">
                        <option value="">Pilih CPL</option>
                        @foreach($cpls as $cpl)
                            <option value="{{ $cpl->id }}" data-desk="{{ $cpl->desk }}">{{ $cpl->id }}</option>
                        @endforeach
                    </select>
                    <input type="text" id="id_cpl_display" class="form-input-modal" readonly style="display: none;">
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpl">DESKRIPSI CPL <span class="required">*</span></label>
                    <textarea id="desk_cpl" name="desk_cpl" class="form-input-modal" rows="4" required readonly></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpmk">KODE CPMK <span class="required">*</span></label>
                    <input type="text" id="id_cpmk" name="id_cpmk" class="form-input-modal" required>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpmk">CPMK <span class="required">*</span></label>
                    <textarea id="desk_cpmk" name="desk_cpmk" class="form-input-modal" rows="3" required></textarea>
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
        // Toggle submenu
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

        // Update desk_cpl based on selected CPL
        function updateDeskCpl() {
            const select = document.getElementById('id_cpl');
            const selectedOption = select.options[select.selectedIndex];
            const deskCplInput = document.getElementById('desk_cpl');
            const idCplHidden = document.getElementById('id_cpl_hidden');

            if (selectedOption.value) {
                deskCplInput.value = selectedOption.getAttribute('data-desk') || '';
                idCplHidden.value = selectedOption.value; // Update hidden input
            } else {
                deskCplInput.value = '';
                idCplHidden.value = '';
            }
        }

        // Modal functions
        const modal = document.getElementById('modalForm');
        let selectedIds = [];

        function openModal(mode) {
            const idCplSelect = document.getElementById('id_cpl');
            const idCplDisplay = document.getElementById('id_cpl_display');
            const idCplHidden = document.getElementById('id_cpl_hidden');

            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("pemetaan-cpl-cpmk-mk.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('formDeskCpmkOld').value = '';
                document.getElementById('desk_cpl').value = '';
                idCplHidden.value = '';

                // Show select, hide display for create mode
                idCplSelect.style.display = 'block';
                idCplDisplay.style.display = 'none';
                idCplSelect.disabled = false;
                idCplSelect.required = true;
                idCplSelect.name = 'id_cpl'; // Ensure select has name for create
                idCplHidden.name = ''; // Remove name from hidden input
            } else if (mode === 'edit') {
                const checkboxes = document.querySelectorAll('.row-checkbox:checked');
                if (checkboxes.length !== 1) {
                    alert('Pilih satu data untuk diedit!');
                    return;
                }
                const deskCpmk = checkboxes[0].value;
                fetch(`/pemetaan-cpl-cpmk-mk/${encodeURIComponent(deskCpmk)}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modalTitle').textContent = 'Edit Data';
                        document.getElementById('formData').action = `/pemetaan-cpl-cpmk-mk/${encodeURIComponent(deskCpmk)}`;
                        document.getElementById('formMethod').value = 'PUT';
                        document.getElementById('formDeskCpmkOld').value = data.desk_cpmk;

                        // Set CPL values but make them readonly
                        idCplSelect.value = data.id_cpl;
                        idCplHidden.value = data.id_cpl; // Store in hidden input for form submission
                        document.getElementById('desk_cpl').value = data.desk_cpl;

                        // Show display input, hide select for edit mode
                        idCplSelect.style.display = 'none';
                        idCplDisplay.style.display = 'block';
                        idCplDisplay.value = data.id_cpl;

                        // Make CPL select disabled and remove name, use hidden input instead
                        idCplSelect.disabled = true;
                        idCplSelect.required = false;
                        idCplSelect.name = ''; // Remove name from select
                        idCplHidden.name = 'id_cpl'; // Set name on hidden input

                        // Set CPMK values (editable)
                        document.getElementById('id_cpmk').value = data.id_cpmk;
                        document.getElementById('desk_cpmk').value = data.desk_cpmk;
                        document.getElementById('id_mk').value = data.id_mk;

                        modal.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal memuat data!');
                    });
                return;
            }
            modal.style.display = 'block';
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
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateButtons();
        }

        function updateButtons() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const btnEdit = document.getElementById('btnEdit');
            const btnDelete = document.getElementById('btnDelete');

            if (checkboxes.length === 1) {
                btnEdit.disabled = false;
                btnDelete.disabled = false;
            } else if (checkboxes.length > 1) {
                btnEdit.disabled = true;
                btnDelete.disabled = false;
            } else {
                btnEdit.disabled = true;
                btnDelete.disabled = true;
            }
        }

        // Delete function
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
            form.action = '{{ route("pemetaan-cpl-cpmk-mk.destroy-multiple") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            checkboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Handle form submission for PUT method
        document.getElementById('formData').addEventListener('submit', function(e) {
            if (document.getElementById('formMethod').value === 'PUT') {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const action = form.action;

                // Create a new form for PUT request
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
