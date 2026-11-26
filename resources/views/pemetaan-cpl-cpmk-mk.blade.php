<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemetaan CPMK - Kurikulum UKRI</title>
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
        <!-- Left Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                @include('partials.sidebar-nav')
            </nav>
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

            @include('partials.page-footer')
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
                <div class="form-group-modal">
                    <label for="source_desk_cpmk">PILIH CPMK (Sumber Mst CPMK) <span class="required">*</span></label>
                    <select id="source_desk_cpmk" name="source_desk_cpmk" class="form-input-modal" required onchange="applySelectedCpmkData()">
                        <option value="">Pilih CPMK</option>
                        @foreach($cpmkMaster as $cpmk)
                            <option
                                value="{{ $cpmk->desk_cpmk }}"
                                data-id-cpl="{{ $cpmk->id_cpl }}"
                                data-desk-cpl="{{ $cpmk->desk_cpl }}"
                                data-id-cpmk="{{ $cpmk->id_cpmk }}"
                                data-desk-cpmk="{{ $cpmk->desk_cpmk }}">
                                {{ $cpmk->id_cpmk }} - {{ \Illuminate\Support\Str::limit($cpmk->desk_cpmk, 70) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpl_display">CPL</label>
                    <input type="text" id="id_cpl_display" class="form-input-modal" readonly>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpl_display">DESKRIPSI CPL</label>
                    <textarea id="desk_cpl_display" class="form-input-modal" rows="4" readonly></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpmk_display">KODE CPMK</label>
                    <input type="text" id="id_cpmk_display" class="form-input-modal" readonly>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpmk_display">CPMK</label>
                    <textarea id="desk_cpmk_display" class="form-input-modal" rows="3" readonly></textarea>
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

        // Fill CPL/CPMK fields from selected master CPMK
        function applySelectedCpmkData() {
            const select = document.getElementById('source_desk_cpmk');
            const selectedOption = select.options[select.selectedIndex];
            const idCplInput = document.getElementById('id_cpl_display');
            const deskCplInput = document.getElementById('desk_cpl_display');
            const idCpmkInput = document.getElementById('id_cpmk_display');
            const deskCpmkInput = document.getElementById('desk_cpmk_display');

            if (selectedOption.value) {
                idCplInput.value = selectedOption.getAttribute('data-id-cpl') || '';
                deskCplInput.value = selectedOption.getAttribute('data-desk-cpl') || '';
                idCpmkInput.value = selectedOption.getAttribute('data-id-cpmk') || '';
                deskCpmkInput.value = selectedOption.getAttribute('data-desk-cpmk') || '';
            } else {
                idCplInput.value = '';
                deskCplInput.value = '';
                idCpmkInput.value = '';
                deskCpmkInput.value = '';
            }
        }

        // Modal functions
        const modal = document.getElementById('modalForm');

        function openModal(mode) {
            const sourceSelect = document.getElementById('source_desk_cpmk');

            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("pemetaan-cpl-cpmk-mk.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('formDeskCpmkOld').value = '';
                applySelectedCpmkData();
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

                        const hasOption = Array.from(sourceSelect.options).some(option => option.value === data.desk_cpmk);
                        if (!hasOption && data.desk_cpmk) {
                            const fallbackOption = document.createElement('option');
                            fallbackOption.value = data.desk_cpmk;
                            fallbackOption.textContent = `${data.id_cpmk} - ${data.desk_cpmk}`;
                            fallbackOption.setAttribute('data-id-cpl', data.id_cpl);
                            fallbackOption.setAttribute('data-desk-cpl', data.desk_cpl);
                            fallbackOption.setAttribute('data-id-cpmk', data.id_cpmk);
                            fallbackOption.setAttribute('data-desk-cpmk', data.desk_cpmk);
                            sourceSelect.appendChild(fallbackOption);
                        }
                        sourceSelect.value = data.desk_cpmk;
                        applySelectedCpmkData();
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




