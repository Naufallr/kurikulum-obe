<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sub-CPMK - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
            <h1 class="content-title">SUB-CPMK</h1>

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
                            <th>CPMK</th>
                            <th>Deskripsi CPMK</th>
                            <th>ID Sub-CPMK</th>
                            <th>Deskripsi Sub-CPMK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedByCpl = $data->groupBy('id_cpl');
                            $cplDeskMap = $cpls->pluck('desk', 'id');
                        @endphp
                        @forelse($groupedByCpl as $cplId => $cplItems)
                            @php
                                $groupedByCpmk = $cplItems->groupBy('id_cpmk');
                                $cplRowspan = $cplItems->count();
                                $cplFirstPrinted = false;
                            @endphp
                            @foreach($groupedByCpmk as $cpmkId => $cpmkItems)
                                @php
                                    $cpmkRowspan = $cpmkItems->count();
                                    $cpmkFirst = $cpmkItems->first();
                                    $cpmkFirstPrinted = false;
                                @endphp
                                @foreach($cpmkItems as $row)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_ids[]" value="{{ $row->id_subcpmk }}" class="row-checkbox" onchange="updateButtons()">
                                        </td>
                                        @if(!$cplFirstPrinted)
                                            <td rowspan="{{ $cplRowspan }}">
                                                <span>{{ $cplId }}</span>
                                                <i class="fas fa-eye" title="{{ $cplDeskMap[$cplId] ?? '-' }}" style="margin-left: 4px; font-size: 11px; cursor: help;"></i>
                                            </td>
                                            @php $cplFirstPrinted = true; @endphp
                                        @endif
                                        @if(!$cpmkFirstPrinted)
                                            <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkId }}</td>
                                            <td rowspan="{{ $cpmkRowspan }}">{{ $cpmkFirst->desk_cpmk }}</td>
                                            @php $cpmkFirstPrinted = true; @endphp
                                        @endif
                                        <td>{{ $row->id_subcpmk }}</td>
                                        <td>{{ $row->desk_subcpmk }}</td>
                                    </tr>
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

            @include('partials.page-footer')
        </main>
    </div>

    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Data</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="formData" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id_subcpmk_old" id="idSubcpmkOld">

                <input type="hidden" id="id_cpmk" name="id_cpmk">

                <div class="form-group-modal">
                    <label for="source_desk_cpmk">CPMK <span class="required">*</span></label>
                    <select id="source_desk_cpmk" class="form-input-modal" required onchange="updateCpmkFields()">
                        <option value="">Pilih CPMK</option>
                        @foreach($cpmks as $cpmk)
                            <option value="{{ $cpmk->desk_cpmk }}"
                                data-id-cpmk="{{ $cpmk->id_cpmk }}"
                                data-id-cpl="{{ $cpmk->id_cpl }}"
                                data-desk-cpmk="{{ $cpmk->desk_cpmk }}">
                                {{ $cpmk->id_cpmk }} - {{ $cpmk->id_cpl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-modal">
                    <label for="id_cpl">CPL <span class="required">*</span></label>
                    <input type="text" id="id_cpl" name="id_cpl" class="form-input-modal" readonly required>
                </div>

                <div class="form-group-modal">
                    <label for="desk_cpmk">Deskripsi CPMK <span class="required">*</span></label>
                    <textarea id="desk_cpmk" name="desk_cpmk" class="form-input-modal" rows="3" readonly required></textarea>
                </div>

                <div class="form-group-modal">
                    <label for="id_subcpmk">ID Sub-CPMK <span class="required">*</span></label>
                    <input type="text" id="id_subcpmk" name="id_subcpmk" class="form-input-modal" required>
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

        function updateCpmkFields() {
            const select = document.getElementById('source_desk_cpmk');
            const selectedOption = select.options[select.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                document.getElementById('id_cpmk').value = '';
                document.getElementById('id_cpl').value = '';
                document.getElementById('desk_cpmk').value = '';
                return;
            }

            document.getElementById('id_cpmk').value = selectedOption.getAttribute('data-id-cpmk') || '';
            document.getElementById('id_cpl').value = selectedOption.getAttribute('data-id-cpl') || '';
            document.getElementById('desk_cpmk').value = selectedOption.getAttribute('data-desk-cpmk') || '';
        }

        const modal = document.getElementById('modalForm');

        function openModal(mode) {
            if (mode === 'create') {
                document.getElementById('modalTitle').textContent = 'Tambah Data';
                document.getElementById('formData').action = '{{ route("sub-cpmk.store") }}';
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('formData').reset();
                document.getElementById('idSubcpmkOld').value = '';
                return modal.style.display = 'block';
            }

            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length !== 1) {
                alert('Pilih satu data untuk diedit!');
                return;
            }

            const idSubcpmk = checkboxes[0].value;
            fetch(`/sub-cpmk/${encodeURIComponent(idSubcpmk)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Data';
                    document.getElementById('formData').action = `/sub-cpmk/${encodeURIComponent(idSubcpmk)}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('idSubcpmkOld').value = data.id_subcpmk;
                    document.getElementById('id_cpl').value = data.id_cpl;
                    document.getElementById('id_cpmk').value = data.id_cpmk;
                    document.getElementById('source_desk_cpmk').value = data.desk_cpmk;
                    document.getElementById('desk_cpmk').value = data.desk_cpmk;
                    document.getElementById('id_subcpmk').value = data.id_subcpmk;
                    document.getElementById('desk_subcpmk').value = data.desk_subcpmk;
                    modal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
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
        };

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
            form.action = '{{ route("sub-cpmk.destroy-multiple") }}';

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

        document.getElementById('formData').addEventListener('submit', function(e) {
            if (document.getElementById('formMethod').value === 'PUT') {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.action;

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

                for (const [key, value] of formData.entries()) {
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



