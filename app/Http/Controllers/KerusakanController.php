<?php

namespace App\Http\Controllers;

use App\Models\Kerusakan; // Import model Kerusakan
use App\Models\Bencana; // Import model Bencana untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class KerusakanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua data kerusakan dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data kerusakan dengan eager loading bencana, creator, dan changer
        $kerusakanCollection = Kerusakan::with('bencana', 'creator', 'changer')->get();

        // Ambil semua data bencana untuk dropdown di modal
        $bencanaList = Bencana::all();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $kerusakanCollection = $kerusakanCollection->filter(function ($kerusakan) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($kerusakan->objek), $searchLower) ||
                       Str::contains(Str::lower($kerusakan->tingkat_kerusakan), $searchLower) ||
                       Str::contains(Str::lower($kerusakan->satuan), $searchLower) ||
                       Str::contains(Str::lower($kerusakan->deskripsi), $searchLower) ||
                       Str::contains(Str::lower($kerusakan->bencana->nama_bencana ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $kerusakanCollection = $kerusakanCollection->sortByDesc('tanggal_input');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $kerusakanCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $kerusakan = new LengthAwarePaginator($currentItems, $kerusakanCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.kerusakan.index', compact('kerusakan'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat data kerusakan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.kerusakan.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data kerusakan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'objek' => 'required|string|max:255',
            'tingkat_kerusakan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_input' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'objek.required' => 'Objek kerusakan wajib diisi.',
            'tingkat_kerusakan.required' => 'Tingkat Kerusakan wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh negatif.',
            'satuan.required' => 'Satuan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_input.required' => 'Tanggal Input wajib diisi.',
            'tanggal_input.date' => 'Format Tanggal Input tidak valid.',
        ]);

        try {
            $kerusakan = new Kerusakan();
            $kerusakan->bencana_id = $request->bencana_id;
            $kerusakan->objek = $request->objek;
            $kerusakan->tingkat_kerusakan = $request->tingkat_kerusakan;
            $kerusakan->jumlah = $request->jumlah;
            $kerusakan->satuan = $request->satuan;
            $kerusakan->deskripsi = $request->deskripsi;
            $kerusakan->tanggal_input = Carbon::parse($request->tanggal_input);
            // create_who dan create_date akan diisi otomatis oleh boot method
            $kerusakan->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('kerusakan.index')->with('success', 'Data Kerusakan berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding kerusakan: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Data Kerusakan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit data kerusakan.
     *
     * @param  \App\Models\Kerusakan  $kerusakan
     * @return \Illuminate\View\View
     */
    public function edit(Kerusakan $kerusakan)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.kerusakan.edit', compact('kerusakan', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate data kerusakan yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kerusakan  $kerusakan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Kerusakan $kerusakan)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'objek' => 'required|string|max:255',
            'tingkat_kerusakan' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_input' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'objek.required' => 'Objek kerusakan wajib diisi.',
            'tingkat_kerusakan.required' => 'Tingkat Kerusakan wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka.',
            'jumlah.min' => 'Jumlah tidak boleh negatif.',
            'satuan.required' => 'Satuan wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_input.required' => 'Tanggal Input wajib diisi.',
            'tanggal_input.date' => 'Format Tanggal Input tidak valid.',
        ]);

        try {
            $kerusakan->bencana_id = $request->bencana_id;
            $kerusakan->objek = $request->objek;
            $kerusakan->tingkat_kerusakan = $request->tingkat_kerusakan;
            $kerusakan->jumlah = $request->jumlah;
            $kerusakan->satuan = $request->satuan;
            $kerusakan->deskripsi = $request->deskripsi;
            $kerusakan->tanggal_input = Carbon::parse($request->tanggal_input);
            $kerusakan->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('kerusakan.index')->with('success', 'Data Kerusakan berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating kerusakan: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Data Kerusakan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data kerusakan dari database.
     *
     * @param  \App\Models\Kerusakan  $kerusakan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Kerusakan $kerusakan)
    {
        try {
            $kerusakan->delete();

            return response()->json(['success' => true, 'message' => 'Data Kerusakan berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting kerusakan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Data Kerusakan: ' . $e->getMessage()], 500);
        }
    }
}
