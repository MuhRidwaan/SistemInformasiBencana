<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat; // Import model LaporanMasyarakat
use App\Models\Bencana; // Import model Bencana untuk dropdown (opsional)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade
use Illuminate\Support\Facades\Storage; // Untuk upload file

class LaporanMasyarakatController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua laporan masyarakat dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data laporan masyarakat dengan eager loading bencana, creator, dan changer
        $laporanMasyarakatCollection = LaporanMasyarakat::with('bencana', 'creator', 'changer')->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $laporanMasyarakatCollection = $laporanMasyarakatCollection->filter(function ($laporan) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($laporan->jenis_laporan), $searchLower) ||
                       Str::contains(Str::lower($laporan->judul_laporan), $searchLower) ||
                       Str::contains(Str::lower($laporan->nama_pelapor ?? ''), $searchLower) ||
                       Str::contains(Str::lower($laporan->status_laporan), $searchLower) ||
                       Str::contains(Str::lower($laporan->bencana->nama_bencana ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $laporanMasyarakatCollection = $laporanMasyarakatCollection->sortByDesc('tanggal_laporan');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $laporanMasyarakatCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $laporanMasyarakat = new LengthAwarePaginator($currentItems, $laporanMasyarakatCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.laporan_masyarakat.index', compact('laporanMasyarakat'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat laporan masyarakat baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown (opsional)
        return view('admin.laporan_masyarakat.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan laporan masyarakat baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_laporan' => 'required|string|max:50',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'tanggal_laporan' => 'required|date',
            'nama_pelapor' => 'nullable|string|max:255',
            'kontak_pelapor' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'path_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'status_laporan' => 'required|in:Pending,Diterima,Ditolak,Selesai,Diproses', // Tambahkan Diproses
            'bencana_id' => 'nullable|exists:m_bencana,bencana_id',
        ], [
            'jenis_laporan.required' => 'Jenis Laporan wajib diisi.',
            'judul_laporan.required' => 'Judul Laporan wajib diisi.',
            'deskripsi_laporan.required' => 'Deskripsi Laporan wajib diisi.',
            'tanggal_laporan.required' => 'Tanggal Laporan wajib diisi.',
            'tanggal_laporan.date' => 'Format Tanggal Laporan tidak valid.',
            'path_foto.image' => 'File harus berupa gambar.',
            'path_foto.mimes' => 'Format gambar yang diizinkan: jpeg, png, jpg, gif.',
            'path_foto.max' => 'Ukuran gambar maksimal 2MB.',
            'status_laporan.required' => 'Status Laporan wajib diisi.',
            'status_laporan.in' => 'Status Laporan tidak valid.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
        ]);

        try {
            $laporan = new LaporanMasyarakat();
            $laporan->jenis_laporan = $request->jenis_laporan;
            $laporan->judul_laporan = $request->judul_laporan;
            $laporan->deskripsi_laporan = $request->deskripsi_laporan;
            $laporan->tanggal_laporan = Carbon::parse($request->tanggal_laporan);
            $laporan->nama_pelapor = $request->nama_pelapor;
            $laporan->kontak_pelapor = $request->kontak_pelapor;
            $laporan->latitude = $request->latitude;
            $laporan->longitude = $request->longitude;
            $laporan->status_laporan = $request->status_laporan;
            $laporan->bencana_id = $request->bencana_id;

            // Handle file upload
            if ($request->hasFile('path_foto')) {
                $path = $request->file('path_foto')->store('public/laporan_masyarakat_fotos');
                $laporan->path_foto = Storage::url($path); // Simpan URL publik
            }

            // create_who dan create_date akan diisi otomatis oleh boot method di model jika ada
            // Atau isi manual jika tidak ada boot method:
            $laporan->create_who = Auth::id(); // Asumsi user login adalah pembuat
            $laporan->create_date = now();


            $laporan->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('laporan-masyarakat.index')->with('success', 'Laporan Masyarakat berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding laporan masyarakat: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Laporan Masyarakat: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * Menampilkan detail laporan masyarakat.
     *
     * @param  \App\Models\LaporanMasyarakat  $laporanMasyarakat
     * @return \Illuminate\View\View
     */
    public function show(LaporanMasyarakat $laporanMasyarakat)
    {
        // Eager load creator dan changer untuk ditampilkan di detail
        $laporanMasyarakat->load('creator', 'changer', 'bencana');
        return view('admin.laporan_masyarakat.detail', compact('laporanMasyarakat'));
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit laporan masyarakat.
     *
     * @param  \App\Models\LaporanMasyarakat  $laporanMasyarakat
     * @return \Illuminate\View\View
     */
    public function edit(LaporanMasyarakat $laporanMasyarakat)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown (opsional)
        return view('admin.laporan_masyarakat.edit', compact('laporanMasyarakat', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate laporan masyarakat yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LaporanMasyarakat  $laporanMasyarakat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LaporanMasyarakat $laporanMasyarakat)
    {
        // Validasi input
        $request->validate([
            'jenis_laporan' => 'required|string|max:50',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'tanggal_laporan' => 'required|date',
            'nama_pelapor' => 'nullable|string|max:255',
            'kontak_pelapor' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'path_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi file gambar
            'status_laporan' => 'required|in:Pending,Diterima,Ditolak,Selesai,Diproses', // Tambahkan Diproses
            'bencana_id' => 'nullable|exists:m_bencana,bencana_id',
        ], [
            'jenis_laporan.required' => 'Jenis Laporan wajib diisi.',
            'judul_laporan.required' => 'Judul Laporan wajib diisi.',
            'deskripsi_laporan.required' => 'Deskripsi Laporan wajib diisi.',
            'tanggal_laporan.required' => 'Tanggal Laporan wajib diisi.',
            'tanggal_laporan.date' => 'Format Tanggal Laporan tidak valid.',
            'path_foto.image' => 'File harus berupa gambar.',
            'path_foto.mimes' => 'Format gambar yang diizinkan: jpeg, png, jpg, gif.',
            'path_foto.max' => 'Ukuran gambar maksimal 2MB.',
            'status_laporan.required' => 'Status Laporan wajib diisi.',
            'status_laporan.in' => 'Status Laporan tidak valid.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
        ]);

        try {
            $laporanMasyarakat->jenis_laporan = $request->jenis_laporan;
            $laporanMasyarakat->judul_laporan = $request->judul_laporan;
            $laporanMasyarakat->deskripsi_laporan = $request->deskripsi_laporan;
            $laporanMasyarakat->tanggal_laporan = Carbon::parse($request->tanggal_laporan);
            $laporanMasyarakat->nama_pelapor = $request->nama_pelapor;
            $laporanMasyarakat->kontak_pelapor = $request->kontak_pelapor;
            $laporanMasyarakat->latitude = $request->latitude;
            $laporanMasyarakat->longitude = $request->longitude;
            $laporanMasyarakat->status_laporan = $request->status_laporan;
            $laporanMasyarakat->bencana_id = $request->bencana_id;

            // Handle file upload
            if ($request->hasFile('path_foto')) {
                // Hapus foto lama jika ada
                if ($laporanMasyarakat->path_foto) {
                    Storage::delete(Str::replaceFirst('/storage/', 'public/', $laporanMasyarakat->path_foto));
                }
                $path = $request->file('path_foto')->store('public/laporan_masyarakat_fotos');
                $laporanMasyarakat->path_foto = Storage::url($path);
            }

            // change_who dan change_date akan diisi otomatis oleh boot method jika ada
            // Atau isi manual jika tidak ada boot method:
            $laporanMasyarakat->change_who = Auth::id(); // Asumsi user login adalah pengubah
            $laporanMasyarakat->change_date = now();

            $laporanMasyarakat->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('laporan-masyarakat.index')->with('success', 'Laporan Masyarakat berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating laporan masyarakat: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Laporan Masyarakat: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengupdate status laporan masyarakat (Approve, Process, Reject, Complete).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LaporanMasyarakat  $laporanMasyarakat
     * @param  string  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, LaporanMasyarakat $laporanMasyarakat, $status)
    {
        try {
            // Pastikan status yang dikirim adalah salah satu dari yang valid
            $validStatuses = ['Diterima', 'Diproses', 'Selesai', 'Ditolak'];
            if (!in_array($status, $validStatuses)) {
                return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 400);
            }

            $laporanMasyarakat->status_laporan = $status;
            $laporanMasyarakat->change_who = Auth::id(); // User yang melakukan perubahan
            $laporanMasyarakat->change_date = now();
            $laporanMasyarakat->save();

            return response()->json(['success' => true, 'message' => 'Status laporan berhasil diperbarui menjadi ' . $status . '.'], 200);

        } catch (\Exception $e) {
            \Log::error('Error updating status laporan masyarakat: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status laporan: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * Menghapus laporan masyarakat dari database.
     *
     * @param  \App\Models\LaporanMasyarakat  $laporanMasyarakat
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(LaporanMasyarakat $laporanMasyarakat)
    {
        try {
            // Hapus file foto terkait jika ada
            if ($laporanMasyarakat->path_foto) {
                Storage::delete(Str::replaceFirst('/storage/', 'public/', $laporanMasyarakat->path_foto));
            }
            $laporanMasyarakat->delete();

            return response()->json(['success' => true, 'message' => 'Laporan Masyarakat berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting laporan masyarakat: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Laporan Masyarakat: ' . $e->getMessage()], 500);
        }
    }
}
