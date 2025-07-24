<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Carbon\Carbon; // Import Carbon

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'm_dis_kelurahan'; // Sesuaikan dengan nama tabel
    protected $primaryKey = 'kelurahan_id'; // Sesuaikan primary key
    public $timestamps = false; // Nonaktifkan timestamps default Laravel

    protected $fillable = [
        'kecamatan_id',
        'kode_wilayah',
        'nama_kelurahan',
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'change_date' => 'datetime',
    ];

    /**
     * Relasi ke model Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'kecamatan_id');
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
