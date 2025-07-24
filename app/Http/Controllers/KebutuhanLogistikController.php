<?php

namespace App\Http\Controllers;

use App\Models\KebutuhanLogistik;
use App\Models\Bencana; // Import model Bencana
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class KebutuhanLogistikController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua kebutuhan logistik dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data kebutuhan logistik dengan eager loading bencana, creator, dan changer
        $kebutuhanLogistikCollection = KebutuhanLogistik::with('bencana', 'creator', 'changer')->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $kebutuhanLogistikCollection = $kebutuhanLogistikCollection->filter(function ($kebutuhan) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($kebutuhan->jenis_kebutuhan), $searchLower) ||
                       Str::contains(Str::lower($kebutuhan->satuan), $searchLower) ||
                       Str::contains(Str::lower($kebutuhan->deskripsi ?? ''), $searchLower) ||
                       Str::contains(Str::lower($kebutuhan->bencana->nama_bencana ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $kebutuhanLogistikCollection = $kebutuhanLogistikCollection->sortByDesc('tanggal_update');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $kebutuhanLogistikCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $kebutuhanLogistik = new LengthAwarePaginator($currentItems, $kebutuhanLogistikCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        // Hanya passing $kebutuhanLogistik ke view index
        return view('admin.kebutuhan_logistik.index', compact('kebutuhanLogistik'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat kebutuhan logistik baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.kebutuhan_logistik.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kebutuhan logistik baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'jenis_kebutuhan' => 'required|string|max:255',
            'jumlah_dibutuhkan' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'jumlah_tersedia' => 'required|integer|min:0',
            'tanggal_update' => 'required|date',
            'deskripsi' => 'nullable|string',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'jenis_kebutuhan.required' => 'Jenis Kebutuhan wajib diisi.',
            'jumlah_dibutuhkan.required' => 'Jumlah Dibutuhkan wajib diisi.',
            'jumlah_dibutuhkan.integer' => 'Jumlah Dibutuhkan harus berupa angka.',
            'jumlah_dibutuhkan.min' => 'Jumlah Dibutuhkan tidak boleh negatif.',
            'satuan.required' => 'Satuan wajib diisi.',
            'jumlah_tersedia.required' => 'Jumlah Tersedia wajib diisi.',
            'jumlah_tersedia.integer' => 'Jumlah Tersedia harus berupa angka.',
            'jumlah_tersedia.min' => 'Jumlah Tersedia tidak boleh negatif.',
            'tanggal_update.required' => 'Tanggal Update wajib diisi.',
            'tanggal_update.date' => 'Format Tanggal Update tidak valid.',
        ]);

        try {
            $kebutuhan = new KebutuhanLogistik();
            $kebutuhan->bencana_id = $request->bencana_id;
            $kebutuhan->jenis_kebutuhan = $request->jenis_kebutuhan;
            $kebutuhan->jumlah_dibutuhkan = $request->jumlah_dibutuhkan;
            $kebutuhan->satuan = $request->satuan;
            $kebutuhan->jumlah_tersedia = $request->jumlah_tersedia;
            $kebutuhan->tanggal_update = Carbon::parse($request->tanggal_update);
            $kebutuhan->deskripsi = $request->deskripsi;
            $kebutuhan->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('kebutuhan-logistik.index')->with('success', 'Kebutuhan Logistik berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding kebutuhan logistik: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Kebutuhan Logistik: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit kebutuhan logistik.
     *
     * @param  \App\Models\KebutuhanLogistik  $kebutuhanLogistik
     * @return \Illuminate\View\View
     */
    public function edit(KebutuhanLogistik $kebutuhanLogistik)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.kebutuhan_logistik.edit', compact('kebutuhanLogistik', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate kebutuhan logistik yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KebutuhanLogistik  $kebutuhanLogistik
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, KebutuhanLogistik $kebutuhanLogistik)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'jenis_kebutuhan' => 'required|string|max:255',
            'jumlah_dibutuhkan' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'jumlah_tersedia' => 'required|integer|min:0',
            'tanggal_update' => 'required|date',
            'deskripsi' => 'nullable|string',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'jenis_kebutuhan.required' => 'Jenis Kebutuhan wajib diisi.',
            'jumlah_dibutuhkan.required' => 'Jumlah Dibutuhkan wajib diisi.',
            'jumlah_dibutuhkan.integer' => 'Jumlah Dibutuhkan harus berupa angka.',
            'jumlah_dibutuhkan.min' => 'Jumlah Dibutuhkan tidak boleh negatif.',
            'satuan.required' => 'Satuan wajib diisi.',
            'jumlah_tersedia.required' => 'Jumlah Tersedia wajib diisi.',
            'jumlah_tersedia.integer' => 'Jumlah Tersedia harus berupa angka.',
            'jumlah_tersedia.min' => 'Jumlah Tersedia tidak boleh negatif.',
            'tanggal_update.required' => 'Tanggal Update wajib diisi.',
            'tanggal_update.date' => 'Format Tanggal Update tidak valid.',
        ]);

        try {
            $kebutuhanLogistik->bencana_id = $request->bencana_id;
            $kebutuhanLogistik->jenis_kebutuhan = $request->jenis_kebutuhan;
            $kebutuhanLogistik->jumlah_dibutuhkan = $request->jumlah_dibutuhkan;
            $kebutuhanLogistik->satuan = $request->satuan;
            $kebutuhanLogistik->jumlah_tersedia = $request->jumlah_tersedia;
            $kebutuhanLogistik->tanggal_update = Carbon::parse($request->tanggal_update);
            $kebutuhanLogistik->deskripsi = $request->deskripsi;
            $kebutuhanLogistik->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('kebutuhan-logistik.index')->with('success', 'Kebutuhan Logistik berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating kebutuhan logistik: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Kebutuhan Logistik: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus kebutuhan logistik dari database.
     *
     * @param  \App\Models\KebutuhanLogistik  $kebutuhanLogistik
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(KebutuhanLogistik $kebutuhanLogistik)
    {
        try {
            $kebutuhanLogistik->delete();

            return response()->json(['success' => true, 'message' => 'Kebutuhan Logistik berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting kebutuhan logistik: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Kebutuhan Logistik: ' . $e->getMessage()], 500);
        }
    }
}
