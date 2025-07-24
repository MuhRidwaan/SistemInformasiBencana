<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class Role extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang sesuai dengan skema database Anda
    protected $table = 'm_roles';

    // Tentukan primary key jika bukan 'id'
    protected $primaryKey = 'role_id';

    // Tentukan kolom yang bisa diisi secara massal (mass assignable)
    protected $fillable = [
        'nama_role',
        'deskripsi_role',
        'create_who',
        'change_who',
    ];

    // Tentukan kolom tanggal yang tidak perlu di-handle otomatis oleh Eloquent
    // Karena kita punya 'create_date' dan 'change_date' yang di-handle manual atau default di DB
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'create_date' => 'datetime',
        'change_date' => 'datetime',
    ];

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
     * Relasi many-to-many dengan Permission.
     * Ini adalah metode yang diperlukan untuk mengatasi error "Call to undefined relationship [permissions]".
     */
    public function permissions()
    {
        // 'm_role_permissions' adalah nama tabel pivot
        // 'role_id' adalah foreign key di tabel pivot yang merujuk ke model ini
        // 'permission_id' adalah foreign key di tabel pivot yang merujuk ke model Permission
        return $this->belongsToMany(Permission::class, 'm_role_permissions', 'role_id', 'permission_id');
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
            $model->create_date = now(); // Set tanggal dan waktu saat ini
        });

        // Event 'updating' akan dipanggil sebelum data diupdate
        static::updating(function ($model) {
            $model->change_who = Auth::id(); // Ambil ID user yang sedang login
            $model->change_date = now(); // Set tanggal dan waktu saat ini
        });
    }
}
