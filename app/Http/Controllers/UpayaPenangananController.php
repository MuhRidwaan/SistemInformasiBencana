<?php

namespace App\Http\Controllers;

use App\Models\UpayaPenanganan; // Import model UpayaPenanganan
use App\Models\Bencana; // Import model Bencana untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class UpayaPenangananController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua upaya penanganan dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data upaya penanganan dengan eager loading bencana, creator, dan changer
        $upayaPenangananCollection = UpayaPenanganan::with('bencana', 'creator', 'changer')->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $upayaPenangananCollection = $upayaPenangananCollection->filter(function ($upaya) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($upaya->instansi), $searchLower) ||
                       Str::contains(Str::lower($upaya->jenis_upaya), $searchLower) ||
                       Str::contains(Str::lower($upaya->deskripsi), $searchLower) ||
                       Str::contains(Str::lower($upaya->bencana->nama_bencana ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $upayaPenangananCollection = $upayaPenangananCollection->sortByDesc('tanggal_penanganan');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $upayaPenangananCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $upayaPenanganan = new LengthAwarePaginator($currentItems, $upayaPenangananCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.upaya_penanganan.index', compact('upayaPenanganan'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat upaya penanganan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.upaya_penanganan.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan upaya penanganan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'instansi' => 'required|string|max:255',
            'jenis_upaya' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_penanganan' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'instansi.required' => 'Instansi wajib diisi.',
            'jenis_upaya.required' => 'Jenis Upaya wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_penanganan.required' => 'Tanggal Penanganan wajib diisi.',
            'tanggal_penanganan.date' => 'Format Tanggal Penanganan tidak valid.',
        ]);

        try {
            $upaya = new UpayaPenanganan();
            $upaya->bencana_id = $request->bencana_id;
            $upaya->instansi = $request->instansi;
            $upaya->jenis_upaya = $request->jenis_upaya;
            $upaya->deskripsi = $request->deskripsi;
            $upaya->tanggal_penanganan = Carbon::parse($request->tanggal_penanganan);
            // create_who dan create_date akan diisi otomatis oleh boot method
            $upaya->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('upaya-penanganan.index')->with('success', 'Upaya Penanganan berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding upaya penanganan: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Upaya Penanganan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit upaya penanganan.
     *
     * @param  \App\Models\UpayaPenanganan  $upayaPenanganan
     * @return \Illuminate\View\View
     */
    public function edit(UpayaPenanganan $upayaPenanganan)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.upaya_penanganan.edit', compact('upayaPenanganan', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate upaya penanganan yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UpayaPenanganan  $upayaPenanganan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, UpayaPenanganan $upayaPenanganan)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'instansi' => 'required|string|max:255',
            'jenis_upaya' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_penanganan' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'instansi.required' => 'Instansi wajib diisi.',
            'jenis_upaya.required' => 'Jenis Upaya wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_penanganan.required' => 'Tanggal Penanganan wajib diisi.',
            'tanggal_penanganan.date' => 'Format Tanggal Penanganan tidak valid.',
        ]);

        try {
            $upayaPenanganan->bencana_id = $request->bencana_id;
            $upayaPenanganan->instansi = $request->instansi;
            $upayaPenanganan->jenis_upaya = $request->jenis_upaya;
            $upayaPenanganan->deskripsi = $request->deskripsi;
            $upayaPenanganan->tanggal_penanganan = Carbon::parse($request->tanggal_penanganan);
            $upayaPenanganan->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('upaya-penanganan.index')->with('success', 'Upaya Penanganan berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating upaya penanganan: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Upaya Penanganan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus upaya penanganan dari database.
     *
     * @param  \App\Models\UpayaPenanganan  $upayaPenanganan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UpayaPenanganan $upayaPenanganan)
    {
        try {
            $upayaPenanganan->delete();

            return response()->json(['success' => true, 'message' => 'Upaya Penanganan berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting upaya penanganan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Upaya Penanganan: ' . $e->getMessage()], 500);
        }
    }
}
