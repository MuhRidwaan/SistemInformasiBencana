<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'm_permissions';
    protected $primaryKey = 'permission_id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'module_id', // Tambahkan module_id di sini
    ];

    /**
     * Relasi many-to-many dengan Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'm_role_permissions', 'permission_id', 'role_id');
    }

    /**
     * Relasi many-to-one dengan PermissionModule.
     */
    public function module()
    {
        return $this->belongsTo(PermissionModule::class, 'module_id', 'module_id');
    }
}
