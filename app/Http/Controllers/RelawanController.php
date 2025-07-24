<?php

namespace App\Http\Controllers;

use App\Models\Relawan;
use App\Models\User; // Import model User
use App\Models\Role; // Import model Role (jika diperlukan untuk filter)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RelawanController extends Controller
{
    public function index(Request $request)
    {
        $relawanCollection = Relawan::with(['creator', 'changer', 'user.role'])->get();

        $search = $request->query('search');
        if ($search) {
            $relawanCollection = $relawanCollection->filter(function ($relawan) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($relawan->keahlian ?? ''), $searchLower) ||
                       Str::contains(Str::lower($relawan->organisasi ?? ''), $searchLower) ||
                       Str::contains(Str::lower($relawan->user->nama_lengkap ?? ''), $searchLower) ||
                       Str::contains(Str::lower($relawan->user->username ?? ''), $searchLower);
            });
        }

        $relawanCollection = $relawanCollection->sortByDesc('create_date');

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $relawanCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $relawan = new LengthAwarePaginator($currentItems, $relawanCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.relawan.index', compact('relawan'));
    }

    public function create()
    {
        // Dapatkan user yang BELUM memiliki profil relawan
        // Atau, dapatkan user dengan role 'Relawan' yang belum memiliki profil relawan.
        // Asumsi: Ada user dengan role 'Relawan' dan kita ingin melengkapi profil mereka.
        // Jika 'Relawan' adalah nama role, cari ID-nya.
        $relawanRoleId = Role::where('nama_role', 'Relawan')->first()->role_id ?? null;

        $usersWithoutRelawanProfile = collect(); // Inisialisasi sebagai koleksi kosong

        if ($relawanRoleId) {
            $usersWithoutRelawanProfile = User::where('role_id', $relawanRoleId) // Filter berdasarkan role 'Relawan'
                                            ->doesntHave('relawan') // Filter yang belum punya profil relawan
                                            ->get();
        } else {
            // Jika role 'Relawan' tidak ditemukan, mungkin Anda ingin menampilkan semua user yang belum punya profil relawan
            $usersWithoutRelawanProfile = User::doesntHave('relawan')->get();
            // Atau berikan pesan error bahwa role 'Relawan' tidak ditemukan
            // return redirect()->back()->with('error', 'Role "Relawan" tidak ditemukan. Pastikan role tersebut ada di database.');
        }

        return view('admin.relawan.create', compact('usersWithoutRelawanProfile'));
    }

    public function store(Request $request)
    {
        // Validasi hanya untuk user_id, keahlian, dan organisasi
        $request->validate([
            'user_id' => [
                'required',
                'exists:m_users,user_id', // Pastikan user_id ada
                Rule::unique('m_relawan', 'user_id')->where(function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id);
                }), // Pastikan user_id belum punya profil relawan
            ],
            'keahlian' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
        ], [
            'user_id.unique' => 'User yang dipilih sudah memiliki profil relawan.',
        ]);

        try {
            // Buat Relawan baru dengan user_id yang dipilih
            $relawan = new Relawan();
            $relawan->user_id = $request->user_id;
            $relawan->keahlian = $request->keahlian;
            $relawan->organisasi = $request->organisasi;
            $relawan->save();

            return redirect()->route('relawan.index')->with('success', 'Profil relawan berhasil dilengkapi.');

        } catch (\Exception $e) {
            \Log::error('Error creating relawan profile: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat melengkapi profil relawan: ' . $e->getMessage()]);
        }
    }

    public function edit(Relawan $relawan)
    {
        // Eager load user terkait
        $relawan->load('user');
        $user = $relawan->user; // Dapatkan user terkait

        // Hanya tampilkan data relawan dan user (sebagai info)
        return view('admin.relawan.edit', compact('relawan', 'user'));
    }

    public function update(Request $request, Relawan $relawan)
    {
        // Validasi hanya untuk keahlian dan organisasi
        $request->validate([
            'keahlian' => 'nullable|string|max:255',
            'organisasi' => 'nullable|string|max:255',
        ]);

        try {
            // Update data Relawan
            $relawan->keahlian = $request->keahlian;
            $relawan->organisasi = $request->organisasi;
            $relawan->save();

            return redirect()->route('relawan.index')->with('success', 'Profil relawan berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating relawan profile: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profil relawan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Relawan $relawan)
    {
        try {
            // Hapus hanya entri relawan, user terkait TIDAK dihapus
            $relawan->delete();

            return response()->json(['success' => true, 'message' => 'Profil relawan berhasil dihapus. User terkait tidak dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting relawan profile: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus profil relawan: ' . $e->getMessage()], 500);
        }
    }

    // New method to fetch relawan data for a selected user via AJAX
    public function getRelawanDataByUser(Request $request)
    {
        $userId = $request->input('user_id');
        $relawan = Relawan::where('user_id', $userId)->first();

        if ($relawan) {
            return response()->json([
                'exists' => true,
                'keahlian' => $relawan->keahlian,
                'organisasi' => $relawan->organisasi
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    }
}