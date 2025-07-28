<?php

namespace App\Http\Controllers;

use App\Services\AkunPenggunaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AkunPengguna;

class AkunPenggunaController extends Controller
{
    protected AkunPenggunaService $akunService;

    public function __construct(AkunPenggunaService $akunService)
    {
        $this->akunService = $akunService;
    }

    // Daftar semua akun (hanya superadmin)
    public function index()
    {
        $this->authorizeRole(['superadmin']);

        $akun = $this->akunService->getAll();

        $countSuperadmin = $akun->where('role', 'superadmin')->count();
        $countAdmin = $akun->where('role', 'admin')->count();
        $countStaff = $akun->where('role', 'staff')->count();

        return view('akun_pengguna.index', compact(
            'akun',
            'countSuperadmin',
            'countAdmin',
            'countStaff'
        ));
    }

    public function create()
    {
        $this->authorizeRole(['superadmin']);
        return view('akun_pengguna.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['superadmin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255', 
            'email' => 'required|email|unique:akun_pengguna,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,admin,staff',
            'can_manage_users' => 'nullable|boolean',
            'can_manage_data' => 'nullable|boolean',
        ]);

        $this->akunService->create($validated);

        return redirect()->route('akun-pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->authorizeRole(['superadmin']);

        $akun = $this->akunService->findById($id);

        return view('akun_pengguna.edit', compact('akun'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizeRole(['superadmin']);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akun_pengguna,email,' . $id,
            'role' => 'required|in:superadmin,admin,staff',
            'password' => 'nullable|string|min:6',
            'can_manage_users' => 'nullable|boolean',
            'can_manage_data' => 'nullable|boolean',
        ]);

        $this->akunService->update($id, $validated);

        return redirect()->route('akun-pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->authorizeRole(['superadmin']);

        $this->akunService->delete($id);

        return redirect()->route('akun-pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function show($id)
    {
        return redirect()->route('akun-pengguna.index');
    }

    public function settings()
    {
        /** @var AkunPengguna $user */
        $user = Auth::user();
        return view('akun_pengguna.settings', compact('user'));
    }

public function updateSettings(Request $request)
{
    /** @var AkunPengguna $user */
    $user = Auth::user();

    // Aturan validasi dinamis
    $rules = [
        'name' => 'required|string|max:255',
        'password' => 'nullable|string|min:6|confirmed',
    ];

    // Email hanya bisa diubah oleh superadmin
    if ($user->role === 'superadmin') {
        $rules['email'] = 'required|email|unique:akun_pengguna,email,' . $user->id;
    }

    $validated = $request->validate($rules);

    $user->name = $validated['name'];

    if ($user->role === 'superadmin' && isset($validated['email'])) {
        $user->email = $validated['email'];
    }

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()->route('akun-pengguna.settings')->with('success', 'Akun berhasil diperbarui.');
}

    // Form login
    public function showLoginForm()
    {
        return view('pengguna.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Otorisasi berdasarkan role (tanpa middleware).
     *
     * @param array $allowedRoles
     */
    private function authorizeRole(array $allowedRoles): void
    {
        /** @var AkunPengguna|null $user */
        $user = Auth::user();

        if (!$user || !in_array($user->role, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }
}
