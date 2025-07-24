<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionModule extends Model
{
    use HasFactory;

    protected $table = 'm_permission_modules'; // Sesuaikan dengan nama tabel migrasi
    protected $primaryKey = 'module_id'; // Sesuaikan primary key
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi one-to-many dengan Permission.
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id', 'module_id');
    }
}
