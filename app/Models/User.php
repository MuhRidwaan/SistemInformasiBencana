<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'm_users';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model should be timestamped.
     * Laravel's default timestamps (created_at, updated_at) are not used here.
     *
     * @var bool
     */
    public $timestamps = false; // Disable default timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password_hash',
        'nama_lengkap',
        'email',
        'kontak',
        'is_active',
        'role_id', // Pastikan role_id ada di sini
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash', // Hide password_hash when converting to array/JSON
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'create_date' => 'datetime',
        'change_date' => 'datetime',
    ];

    /**
     * Mutator for password_hash to automatically hash the password.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordHashAttribute($value)
    {
        // Only hash if the password is not already hashed (e.g., when editing and password field is empty)
        // Or if it's a new password
        if (!empty($value) && !Hash::needsRehash($value)) {
             $this->attributes['password_hash'] = Hash::make($value);
        } else if (!empty($value)) {
            $this->attributes['password_hash'] = Hash::make($value);
        }
    }

    /**
     * Define relationship with the creator user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_who', 'user_id');
    }

    /**
     * Define relationship with the changer user.
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'change_who', 'user_id');
    }

    /**
     * Define relationship with the Role model.
     */
    public function role()
    {
        // Relasi Many-to-One: Satu User memiliki satu Role
        // 'role_id' adalah foreign key di tabel 'm_users'
        // 'role_id' adalah primary key di tabel 'm_roles'
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // Tambahkan metode untuk memeriksa izin
    public function hasPermissionTo($permissionName)
    {
        // Pastikan user memiliki role dan role tersebut memiliki permissions
        if ($this->role && $this->role->permissions) {
            return $this->role->permissions->contains('name', $permissionName);
        }
        return false;
    }

    // Tambahkan metode getAuthPassword() untuk memberitahu Laravel kolom password Anda
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

       // Pastikan relasi hasOne ke Relawan ada
    public function relawan()
    {
        return $this->hasOne(Relawan::class, 'user_id', 'user_id');
    }
}
