<?php

namespace App\Http\Controllers;

use App\Models\Korban; // Import model Korban
use App\Models\Bencana; // Import model Bencana untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class KorbanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua data korban dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data korban dengan eager loading bencana, creator, dan changer
        $korbanCollection = Korban::with('bencana', 'creator', 'changer')->get();

        // Ambil semua data bencana untuk dropdown di modal
        $bencanaList = Bencana::all();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $korbanCollection = $korbanCollection->filter(function ($korban) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($korban->bencana->nama_bencana ?? ''), $searchLower) ||
                       Str::contains(Str::lower($korban->tanggal_input->format('d M Y') ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $korbanCollection = $korbanCollection->sortByDesc('tanggal_input');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $korbanCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $korban = new LengthAwarePaginator($currentItems, $korbanCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.korban.index', compact('korban'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat data korban baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.korban.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data korban baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'meninggal' => 'required|integer|min:0',
            'luka_berat' => 'required|integer|min:0',
            'luka_ringan' => 'required|integer|min:0',
            'hilang' => 'required|integer|min:0',
            'mengungsi' => 'required|integer|min:0',
            'terdampak' => 'required|integer|min:0',
            'tanggal_input' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'meninggal.required' => 'Jumlah Meninggal wajib diisi.',
            'meninggal.integer' => 'Jumlah Meninggal harus berupa angka.',
            'meninggal.min' => 'Jumlah Meninggal tidak boleh negatif.',
            'luka_berat.required' => 'Jumlah Luka Berat wajib diisi.',
            'luka_berat.integer' => 'Jumlah Luka Berat harus berupa angka.',
            'luka_berat.min' => 'Jumlah Luka Berat tidak boleh negatif.',
            'luka_ringan.required' => 'Jumlah Luka Ringan wajib diisi.',
            'luka_ringan.integer' => 'Jumlah Luka Ringan harus berupa angka.',
            'luka_ringan.min' => 'Jumlah Luka Ringan tidak boleh negatif.',
            'hilang.required' => 'Jumlah Hilang wajib diisi.',
            'hilang.integer' => 'Jumlah Hilang harus berupa angka.',
            'hilang.min' => 'Jumlah Hilang tidak boleh negatif.',
            'mengungsi.required' => 'Jumlah Mengungsi wajib diisi.',
            'mengungsi.integer' => 'Jumlah Mengungsi harus berupa angka.',
            'mengungsi.min' => 'Jumlah Mengungsi tidak boleh negatif.',
            'terdampak.required' => 'Jumlah Terdampak wajib diisi.',
            'terdampak.integer' => 'Jumlah Terdampak harus berupa angka.',
            'terdampak.min' => 'Jumlah Terdampak tidak boleh negatif.',
            'tanggal_input.required' => 'Tanggal Input wajib diisi.',
            'tanggal_input.date' => 'Format Tanggal Input tidak valid.',
        ]);

        try {
            $korban = new Korban();
            $korban->bencana_id = $request->bencana_id;
            $korban->meninggal = $request->meninggal;
            $korban->luka_berat = $request->luka_berat;
            $korban->luka_ringan = $request->luka_ringan;
            $korban->hilang = $request->hilang;
            $korban->mengungsi = $request->mengungsi;
            $korban->terdampak = $request->terdampak;
            $korban->tanggal_input = Carbon::parse($request->tanggal_input);
            // create_who dan create_date akan diisi otomatis oleh boot method
            $korban->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('korban.index')->with('success', 'Data Korban berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding korban: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Data Korban: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit data korban.
     *
     * @param  \App\Models\Korban  $korban
     * @return \Illuminate\View\View
     */
    public function edit(Korban $korban)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.korban.edit', compact('korban', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate data korban yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Korban  $korban
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Korban $korban)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'meninggal' => 'required|integer|min:0',
            'luka_berat' => 'required|integer|min:0',
            'luka_ringan' => 'required|integer|min:0',
            'hilang' => 'required|integer|min:0',
            'mengungsi' => 'required|integer|min:0',
            'terdampak' => 'required|integer|min:0',
            'tanggal_input' => 'required|date',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'meninggal.required' => 'Jumlah Meninggal wajib diisi.',
            'meninggal.integer' => 'Jumlah Meninggal harus berupa angka.',
            'meninggal.min' => 'Jumlah Meninggal tidak boleh negatif.',
            'luka_berat.required' => 'Jumlah Luka Berat wajib diisi.',
            'luka_berat.integer' => 'Jumlah Luka Berat harus berupa angka.',
            'luka_berat.min' => 'Jumlah Luka Berat tidak boleh negatif.',
            'luka_ringan.required' => 'Jumlah Luka Ringan wajib diisi.',
            'luka_ringan.integer' => 'Jumlah Luka Ringan harus berupa angka.',
            'luka_ringan.min' => 'Jumlah Luka Ringan tidak boleh negatif.',
            'hilang.required' => 'Jumlah Hilang wajib diisi.',
            'hilang.integer' => 'Jumlah Hilang harus berupa angka.',
            'hilang.min' => 'Jumlah Hilang tidak boleh negatif.',
            'mengungsi.required' => 'Jumlah Mengungsi wajib diisi.',
            'mengungsi.integer' => 'Jumlah Mengungsi harus berupa angka.',
            'mengungsi.min' => 'Jumlah Mengungsi tidak boleh negatif.',
            'terdampak.required' => 'Jumlah Terdampak wajib diisi.',
            'terdampak.integer' => 'Jumlah Terdampak harus berupa angka.',
            'terdampak.min' => 'Jumlah Terdampak tidak boleh negatif.',
            'tanggal_input.required' => 'Tanggal Input wajib diisi.',
            'tanggal_input.date' => 'Format Tanggal Input tidak valid.',
        ]);

        try {
            $korban->bencana_id = $request->bencana_id;
            $korban->meninggal = $request->meninggal;
            $korban->luka_berat = $request->luka_berat;
            $korban->luka_ringan = $request->luka_ringan;
            $korban->hilang = $request->hilang;
            $korban->mengungsi = $request->mengungsi;
            $korban->terdampak = $request->terdampak;
            $korban->tanggal_input = Carbon::parse($request->tanggal_input);
            $korban->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('korban.index')->with('success', 'Data Korban berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating korban: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Data Korban: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data korban dari database.
     *
     * @param  \App\Models\Korban  $korban
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Korban $korban)
    {
        try {
            $korban->delete();

            return response()->json(['success' => true, 'message' => 'Data Korban berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting korban: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Data Korban: ' . $e->getMessage()], 500);
        }
    }
}
