<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index()
    {
        $this->ensureAdminAccess();

        $users = User::with('roles')->orderBy('name')->get();

        return view('admin-users', compact('users'));
    }

    public function store(Request $request)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'password' => ['required', 'confirmed', Password::min(5)],
            'role' => 'required|in:admin,user',
            'fakultas' => 'nullable|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:255',
            'jenjang_pendidikan' => 'nullable|string|max:255',
            'gelar_lulusan' => 'nullable|string|max:255',
            'visi_keilmuan_program_studi' => 'nullable|string',
            'misi_program_studi' => 'nullable|string',
        ]);

        $payload = [
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'fakultas' => $validated['fakultas'] ?? null,
            'program_studi' => $validated['program_studi'] ?? null,
            'akreditasi' => $validated['akreditasi'] ?? null,
            'jenjang_pendidikan' => $validated['jenjang_pendidikan'] ?? null,
            'gelar_lulusan' => $validated['gelar_lulusan'] ?? null,
            'visi_keilmuan_program_studi' => $validated['visi_keilmuan_program_studi'] ?? null,
            'misi_program_studi' => $validated['misi_program_studi'] ?? null,
        ];

        try {
            $user = User::create($payload);
        } catch (QueryException $exception) {
            if (!$this->isUsersPrimaryKeySequenceError($exception)) {
                throw $exception;
            }

            $this->syncUsersPrimaryKeySequence();
            $user = User::create($payload);
        }

        Role::findOrCreate($validated['role'], 'web');
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(5)],
            'role' => 'required|in:admin,user',
            'fakultas' => 'nullable|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:255',
            'jenjang_pendidikan' => 'nullable|string|max:255',
            'gelar_lulusan' => 'nullable|string|max:255',
            'visi_keilmuan_program_studi' => 'nullable|string',
            'misi_program_studi' => 'nullable|string',
        ]);

        $payload = [
            'name' => $validated['name'],
            'fakultas' => $validated['fakultas'] ?? null,
            'program_studi' => $validated['program_studi'] ?? null,
            'akreditasi' => $validated['akreditasi'] ?? null,
            'jenjang_pendidikan' => $validated['jenjang_pendidikan'] ?? null,
            'gelar_lulusan' => $validated['gelar_lulusan'] ?? null,
            'visi_keilmuan_program_studi' => $validated['visi_keilmuan_program_studi'] ?? null,
            'misi_program_studi' => $validated['misi_program_studi'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        Role::findOrCreate($validated['role'], 'web');
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diupdate.');
    }

    private function ensureAdminAccess(): void
    {
        if (!Auth::check()) {
            abort(401);
        }

        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Halaman ini hanya dapat diakses admin.');
        }
    }

    private function isUsersPrimaryKeySequenceError(QueryException $exception): bool
    {
        return ($exception->getCode() === '23505')
            && str_contains($exception->getMessage(), 'users_pkey');
    }

    private function syncUsersPrimaryKeySequence(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $sequenceName = DB::scalar("SELECT pg_get_serial_sequence('users', 'id')");

        if (!$sequenceName) {
            return;
        }

        $maxId = (int) User::max('id');
        $setValue = max($maxId, 1);
        $isCalled = $maxId > 0 ? 'true' : 'false';

        DB::statement("SELECT setval('{$sequenceName}', {$setValue}, {$isCalled})");
    }
}
