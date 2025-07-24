<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Carbon\Carbon; // Import Carbon

class LaporanMasyarakat extends Model
{
    use HasFactory;

    protected $table = 't_laporan_masyarakat'; // Sesuaikan dengan nama tabel
    protected $primaryKey = 'laporan_id'; // Sesuaikan primary key
    public $timestamps = false; // Nonaktifkan timestamps default Laravel

    protected $fillable = [
        'jenis_laporan',
        'judul_laporan',
        'deskripsi_laporan',
        'tanggal_laporan',
        'nama_pelapor',
        'kontak_pelapor',
        'latitude',
        'longitude',
        'path_foto',
        'status_laporan',
        'bencana_id',
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    protected $casts = [
        'tanggal_laporan' => 'datetime',
        'create_date' => 'datetime',
        'change_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relasi ke model Bencana (opsional).
     */
    public function bencana()
    {
        return $this->belongsTo(Bencana::class, 'bencana_id', 'bencana_id');
    }

    /**
     * Relasi ke model User untuk create_who.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_who', 'user_id');
    }

    /**
     * Relasi ke model User untuk change_who.
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'change_who', 'user_id');
    }

    /**
     * Boot method untuk mengelola otomatis create_who dan change_who
     * serta timestamp create_date dan change_date.
     */
    protected static function boot()
    {
        parent::boot();

        // Event 'creating' akan dipanggil sebelum data disimpan pertama kali
        static::creating(function ($model) {
            $model->create_who = Auth::id(); // Ambil ID user yang sedang login
            $model->create_date = Carbon::now(); // Set tanggal dan waktu saat ini
        });

        // Event 'updating' akan dipanggil sebelum data diupdate
        static::updating(function ($model) {
            $model->change_who = Auth::id(); // Ambil ID user yang sedang login
            $model->change_date = Carbon::now(); // Set tanggal dan waktu saat ini
        });
    }
}
