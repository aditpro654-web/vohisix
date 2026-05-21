<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Traits\ExcelExportTrait;
use App\Http\Controllers\Traits\ExcelImportTrait;

class AdminLoginController extends Controller
{
    use ExcelImportTrait, ExcelExportTrait;
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $sortBy = $request->input('sort_by', 'newest');

        $users = User::query();

        if ($search) {
            $users->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%");
            });
        }

        if ($role) {
            $users->where('role', $role);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $users->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $users->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $users->orderBy('name', 'desc');
                break;
            default: // newest
                $users->orderBy('created_at', 'desc');
        }

        $users = $users->paginate(10);

        // Get statistics
        $totalUser = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalSiswa = User::where('role', 'siswa')->count();

        return view('admin.login.index', compact('users', 'search', 'role', 'sortBy', 'totalUser', 'totalAdmin', 'totalSiswa'));
    }

    /**
     * Show form for creating new user
     */
    public function create()
    {
        return view('admin.login.create');
    }

    /**
     * Store user in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,siswa,wali_kelas,kakonsli',
            'password' => 'required|string|min:6',
            'kelas_id' => 'nullable|required_if:role,wali_kelas,kakonsli|in:XII SIJA 1,XII SIJA 2',
            'kelas_second' => 'nullable|required_if:role,kakonsli|in:XII SIJA 1,XII SIJA 2',
        ]);

        User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'role' => $validated['role'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'kelas_second' => $validated['kelas_second'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.login.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        return view('admin.login.show', compact('user'));
    }

    /**
     * Show form for editing user
     */
    public function edit(User $user)
    {
        return view('admin.login.edit', compact('user'));
    }

    /**
     * Update user in database
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,siswa,wali_kelas,kakonsli',
            'password' => 'nullable|string|min:6',
            'kelas_id' => 'nullable|required_if:role,wali_kelas,kakonsli|in:XII SIJA 1,XII SIJA 2',
            'kelas_second' => 'nullable|required_if:role,kakonsli|in:XII SIJA 1,XII SIJA 2',
        ]);

        $user->name = $validated['name'];
        $user->role = $validated['role'];
        $user->kelas_id = $validated['kelas_id'] ?? null;
        $user->kelas_second = $validated['kelas_second'] ?? null;

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.login.index')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Cegah penghapusan admin pertama
        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus admin terakhir');
        }

        $user->delete();

        return redirect()->route('admin.login.index')->with('success', 'User berhasil dihapus');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::query();
        if ($search) {
            $users->where(function ($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('name', 'like', "%$search%");
            });
        }
        if ($role) {
            $users->where('role', $role);
        }

        $users = $users->orderBy('created_at', 'desc')->get();
        $rows = $users->map(function ($user) {
            return [
                $user->username,
                $user->name,
                $user->role,
                $user->kelas_id,
                $user->kelas_second,
            ];
        })->toArray();

        return $this->streamCsvDownload(
            'users_export_' . now()->format('Y-m-d') . '.csv',
            ['Username', 'Nama', 'Role', 'Kelas Utama', 'Kelas Kedua'],
            $rows
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $rows = $this->parseImportFile($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.login.index')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header username, name, role, password.');
        }

        $imported = 0;
        $skipped = 0;
        foreach ($rows as $row) {
            $username = $row['username'] ?? null;
            $name = $row['name'] ?? null;
            $role = $row['role'] ?? null;
            $password = $row['password'] ?? null;
            $kelasId = $row['kelas_id'] ?? null;
            $kelasSecond = $row['kelas_second'] ?? null;

            if (!$username || !$name || !$role || !$password || !in_array($role, ['admin', 'siswa', 'wali_kelas', 'kakonsli'])) {
                $skipped++;
                continue;
            }

            if (in_array($role, ['wali_kelas', 'kakonsli'], true) && !in_array($kelasId, ['XII SIJA 1', 'XII SIJA 2'], true)) {
                $skipped++;
                continue;
            }

            if ($role === 'kakonsli' && !in_array($kelasSecond, ['XII SIJA 1', 'XII SIJA 2'], true)) {
                $skipped++;
                continue;
            }

            $user = User::where('username', $username)->first();
            if ($user) {
                $user->update([
                    'name' => $name,
                    'role' => $role,
                    'kelas_id' => $kelasId,
                    'kelas_second' => $kelasSecond,
                    'password' => Hash::make($password),
                ]);
                $skipped++;
                continue;
            }

            User::create([
                'username' => $username,
                'name' => $name,
                'role' => $role,
                'kelas_id' => $kelasId,
                'kelas_second' => $kelasSecond,
                'password' => Hash::make($password),
            ]);
            $imported++;
        }

        $message = "$imported user berhasil diimpor";
        if ($skipped > 0) {
            $message .= ", $skipped baris dilewati karena format tidak lengkap atau username sudah ada";
        }

        return redirect()->route('admin.login.index')->with('success', $message);
    }
}
