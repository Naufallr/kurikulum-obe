<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Users - Kurikulum UKRI</title>
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
                        <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
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
            @php
                $formMode = old('form_mode', 'create');
                $editingUserId = old('editing_user_id');
                $isEditMode = $formMode === 'edit' && !empty($editingUserId);
                $formAction = $isEditMode
                    ? route('admin.users.update', ['user' => $editingUserId])
                    : route('admin.users.store');
            @endphp

            <h1 class="content-title">MANAJEMEN USER</h1>

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
                <button class="btn-action btn-tambah" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>USERNAME</th>
                            <th>ROLE</th>
                            <th>FAKULTAS</th>
                            <th>PROGRAM STUDI</th>
                            <th>AKREDITASI</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->getRoleNames()->implode(', ') ?: '-' }}</td>
                                <td>{{ $user->fakultas ?? '-' }}</td>
                                <td>{{ $user->program_studi ?? '-' }}</td>
                                <td>{{ $user->akreditasi ?? '-' }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn-table-edit"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-role="{{ $user->getRoleNames()->first() ?? 'user' }}"
                                        data-user-fakultas="{{ $user->fakultas ?? '' }}"
                                        data-user-program-studi="{{ $user->program_studi ?? '' }}"
                                        data-user-akreditasi="{{ $user->akreditasi ?? '' }}"
                                        data-user-jenjang-pendidikan="{{ $user->jenjang_pendidikan ?? '' }}"
                                        data-user-gelar-lulusan="{{ $user->gelar_lulusan ?? '' }}"
                                        data-user-visi-keilmuan-program-studi="{{ $user->visi_keilmuan_program_studi ?? '' }}"
                                        data-user-misi-program-studi="{{ $user->misi_program_studi ?? '' }}"
                                        onclick="openEditModal(this)"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 20px;">
                                    <em>Belum ada user</em>
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
        <div class="modal-content modal-content--wide">
            <div class="modal-header">
                <h2 id="modalTitle">{{ $isEditMode ? 'Edit User' : 'Tambah User Baru' }}</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="userForm" method="POST" action="{{ $formAction }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="{{ $isEditMode ? 'PUT' : 'POST' }}">
                <input type="hidden" name="form_mode" id="formMode" value="{{ $isEditMode ? 'edit' : 'create' }}">
                <input type="hidden" name="editing_user_id" id="editingUserId" value="{{ $editingUserId }}">

                <div class="form-grid">
                    <div class="form-group-modal">
                        <label for="name">Username <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-input-modal" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group-modal">
                        <label for="role">Role <span class="required">*</span></label>
                        <select id="role" name="role" class="form-input-modal" required>
                            <option value="user" @selected(old('role') === 'user')>User</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        </select>
                    </div>

                    <div class="form-group-modal">
                        <label for="password">Password <span class="required password-required-marker" @if(!$isEditMode) style="display: inline;" @else style="display: none;" @endif>*</span></label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" class="form-input-modal form-input-modal--password" {{ $isEditMode ? '' : 'required' }}>
                            <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password" onclick="togglePasswordVisibility(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-hint" id="passwordHint">{{ $isEditMode ? 'Kosongkan jika password tidak ingin diubah.' : 'Masukkan password untuk user baru.' }}</div>
                    </div>

                    <div class="form-group-modal">
                        <label for="password_confirmation">Konfirmasi Password <span class="required password-required-marker" @if(!$isEditMode) style="display: inline;" @else style="display: none;" @endif>*</span></label>
                        <div class="password-field">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input-modal form-input-modal--password" {{ $isEditMode ? '' : 'required' }}>
                            <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password" onclick="togglePasswordVisibility(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label for="fakultas">Fakultas</label>
                        <input type="text" id="fakultas" name="fakultas" class="form-input-modal" value="{{ old('fakultas') }}">
                    </div>

                    <div class="form-group-modal">
                        <label for="program_studi">Program Studi</label>
                        <input type="text" id="program_studi" name="program_studi" class="form-input-modal" value="{{ old('program_studi') }}">
                    </div>

                    <div class="form-group-modal">
                        <label for="akreditasi">Akreditasi</label>
                        <input type="text" id="akreditasi" name="akreditasi" class="form-input-modal" value="{{ old('akreditasi') }}">
                    </div>

                    <div class="form-group-modal">
                        <label for="jenjang_pendidikan">Jenjang Pendidikan</label>
                        <input type="text" id="jenjang_pendidikan" name="jenjang_pendidikan" class="form-input-modal" value="{{ old('jenjang_pendidikan') }}">
                    </div>

                    <div class="form-group-modal">
                        <label for="gelar_lulusan">Gelar Lulusan</label>
                        <input type="text" id="gelar_lulusan" name="gelar_lulusan" class="form-input-modal" value="{{ old('gelar_lulusan') }}">
                    </div>

                    <div class="form-group-modal form-group-modal--full">
                        <label for="visi_keilmuan_program_studi">Visi Keilmuan Program Studi</label>
                        <textarea id="visi_keilmuan_program_studi" name="visi_keilmuan_program_studi" class="form-input-modal" rows="4">{{ old('visi_keilmuan_program_studi') }}</textarea>
                    </div>

                    <div class="form-group-modal form-group-modal--full">
                        <label for="misi_program_studi">Misi Program Studi</label>
                        <textarea id="misi_program_studi" name="misi_program_studi" class="form-input-modal" rows="4">{{ old('misi_program_studi') }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save" id="submitButton">{{ $isEditMode ? 'Update User' : 'Simpan User' }}</button>
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

        const modal = document.getElementById('modalForm');
        const form = document.getElementById('userForm');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const formMode = document.getElementById('formMode');
        const editingUserId = document.getElementById('editingUserId');
        const submitButton = document.getElementById('submitButton');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordHint = document.getElementById('passwordHint');
        const passwordRequiredMarkers = document.querySelectorAll('.password-required-marker');

        function setPasswordMode(isRequired) {
            passwordInput.required = isRequired;
            passwordConfirmationInput.required = isRequired;
            passwordRequiredMarkers.forEach(marker => {
                marker.style.display = isRequired ? 'inline' : 'none';
            });
            passwordHint.textContent = isRequired
                ? 'Masukkan password untuk user baru.'
                : 'Kosongkan jika password tidak ingin diubah.';
        }

        function resetFormFields() {
            document.getElementById('name').value = '';
            document.getElementById('role').value = 'user';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
            document.getElementById('fakultas').value = '';
            document.getElementById('program_studi').value = '';
            document.getElementById('akreditasi').value = '';
            document.getElementById('jenjang_pendidikan').value = '';
            document.getElementById('gelar_lulusan').value = '';
            document.getElementById('visi_keilmuan_program_studi').value = '';
            document.getElementById('misi_program_studi').value = '';
        }

        function openCreateModal() {
            resetFormFields();
            form.action = '{{ route('admin.users.store') }}';
            formMethod.value = 'POST';
            formMode.value = 'create';
            editingUserId.value = '';
            modalTitle.textContent = 'Tambah User Baru';
            submitButton.textContent = 'Simpan User';
            setPasswordMode(true);
            modal.style.display = 'block';
        }

        function openEditModal(button) {
            const user = button.dataset;
            form.action = `/admin/users/${user.userId}`;
            formMethod.value = 'PUT';
            formMode.value = 'edit';
            editingUserId.value = user.userId;
            modalTitle.textContent = 'Edit User';
            submitButton.textContent = 'Update User';

            document.getElementById('name').value = user.userName || '';
            document.getElementById('role').value = user.userRole || 'user';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
            document.getElementById('fakultas').value = user.userFakultas || '';
            document.getElementById('program_studi').value = user.userProgramStudi || '';
            document.getElementById('akreditasi').value = user.userAkreditasi || '';
            document.getElementById('jenjang_pendidikan').value = user.userJenjangPendidikan || '';
            document.getElementById('gelar_lulusan').value = user.userGelarLulusan || '';
            document.getElementById('visi_keilmuan_program_studi').value = user.userVisiKeilmuanProgramStudi || '';
            document.getElementById('misi_program_studi').value = user.userMisiProgramStudi || '';

            setPasswordMode(false);
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function togglePasswordVisibility(button) {
            const targetId = button.dataset.target;
            const input = document.getElementById(targetId);
            const icon = button.querySelector('i');
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
            button.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                closeModal();
            }
        };
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

        @if($errors->any())
            modal.style.display = 'block';
        @endif
    </script>
    <script src="{{ asset('js/blade-animations.js') }}" defer></script>
</body>
</html>


