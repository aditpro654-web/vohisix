<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Traits\ExcelExportTrait;
use App\Http\Requests\AdminLoginStoreRequest;
use App\Http\Requests\AdminLoginImportPreviewRequest;
use App\Services\Import\UserImportService;

class AdminLoginController extends Controller
{
    use ExcelExportTrait;
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
    public function store(AdminLoginStoreRequest $request)
    {
        $validated = $request->validated();
        $password = $validated['password'] ?? $validated['username'];

        User::create([
            'username' => $validated['username'],
            'name' => $validated['name'],
            'role' => $validated['role'],
            'kelas_id' => $validated['kelas_id'] ?? null,
            'kelas_second' => $validated['kelas_second'] ?? null,
            'password' => Hash::make($password),
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

    public function previewImport(AdminLoginImportPreviewRequest $request, UserImportService $importService)
    {
        $rows = $importService->parseFile($request->file('file'));
        if (empty($rows)) {
            return redirect()->route('admin.login.create')->with('error', 'File tidak dapat dibaca. Gunakan format CSV atau XLSX dengan header yang tepat.');
        }

        $preview = $importService->previewRows($rows);
        Session::put('admin_login_import_preview', $preview['previewRows']);

        return view('admin.login.create', [
            'previewRows' => $preview['previewRows'],
            'previewSummary' => $preview['summary'],
            'previewHeaders' => $preview['headers'],
            'previewMode' => true,
        ]);
    }

    public function import(Request $request, UserImportService $importService)
    {
        $previewRows = Session::get('admin_login_import_preview', []);
        if (empty($previewRows)) {
            return redirect()->route('admin.login.create')->with('error', 'Unggah file import terlebih dahulu untuk melihat preview dan mengonfirmasi data.');
        }

        $result = $importService->importRows($previewRows);
        Session::forget('admin_login_import_preview');

        $message = "{$result['imported']} user berhasil diimpor";
        if ($result['skipped'] > 0) {
            $message .= ", {$result['skipped']} baris dilewati.";
        }

        return redirect()->route('admin.login.index')->with('success', $message);
    }

    public function downloadImportTemplate()
    {
        return $this->streamCsvDownload(
            'user_import_template.csv',
            ['Username', 'Nama', 'Role', 'Password', 'Kelas Utama', 'Kelas Kedua'],
            [
                ['1210001', 'Budi Santoso', 'siswa', '', 'XII SIJA 1', ''],
                ['admin01', 'Admin Sekolah', 'admin', 'password123', '', ''],
                ['wali01', 'Wali Kelas A', 'wali_kelas', 'secret456', 'XII SIJA 1', ''],
                ['konsli01', 'Konsli B', 'kakonsli', 'secret789', 'XII SIJA 1', 'XII SIJA 2'],
            ]
        );
    }
}
