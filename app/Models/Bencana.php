<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Carbon\Carbon; // Import Carbon

class Bencana extends Model
{
    use HasFactory;

    protected $table = 'm_bencana'; // Sesuaikan dengan nama tabel
    protected $primaryKey = 'bencana_id'; // Sesuaikan primary key
    public $timestamps = false; // Nonaktifkan timestamps default Laravel

    protected $fillable = [
        'jenis_bencana_id',
        'nama_bencana',
        'kronologis',
        'deskripsi',
        'tanggal_kejadian',
        'latitude',
        'longitude',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'datetime',
        'create_date' => 'datetime',
        'change_date' => 'datetime',
        'latitude' => 'decimal:8', // Cast sebagai desimal dengan 8 angka di belakang koma
        'longitude' => 'decimal:8', // Cast sebagai desimal dengan 8 angka di belakang koma
    ];

    /**
     * Relasi ke model JenisBencana.
     */
    public function jenisBencana()
    {
        return $this->belongsTo(JenisBencana::class, 'jenis_bencana_id', 'jenis_bencana_id');
    }

    /**
     * Relasi ke model Provinsi.
     */
    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'provinsi_id');
    }

    /**
     * Relasi ke model Kota.
     */
    public function kota()
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'kota_id');
    }

    /**
     * Relasi ke model Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'kecamatan_id');
    }

    /**
     * Relasi ke model Kelurahan.
     */
    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'kelurahan_id');
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
