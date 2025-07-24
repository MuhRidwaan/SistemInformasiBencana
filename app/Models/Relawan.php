<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Relawan extends Model
{
    use HasFactory;

    protected $table = 'm_relawan';
    protected $primaryKey = 'relawan_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'keahlian',
        'organisasi',
        'create_who',
        'create_date',
        'change_who',
        'change_date',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'change_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'create_who', 'user_id');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'change_who', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->create_who = Auth::id();
            }
            $model->create_date = Carbon::now();
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->change_who = Auth::id();
            }
            $model->change_date = Carbon::now();
        });
    }
}