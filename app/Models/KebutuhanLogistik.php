<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Carbon\Carbon; // Import Carbon

class KebutuhanLogistik extends Model
{
    use HasFactory;

    protected $table = 'm_kebutuhan_logistik'; // Sesuaikan dengan nama tabel
    protected $primaryKey = 'kebutuhan_id'; // Sesuaikan primary key
    public $timestamps = false; // Nonaktifkan timestamps default Laravel

    protected $fillable = [
        'bencana_id',
        'jenis_kebutuhan',
        'jumlah_dibutuhkan',
        'satuan',
        'jumlah_tersedia',
        'tanggal_update',
        'deskripsi',
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'create_date' => 'datetime',
        'change_date' => 'datetime',
    ];

    /**
     * Relasi ke model Bencana.
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
