<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function broker()
    {
        return $this->hasOne(Broker::class);
    }

    protected $fillable = [
        'full_name',
        'email',
        'phone_number', // ensure added
        'role',
        'broker_id',
        'password',
        'joined_date',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_password_token',
        'reset_token_expiry',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'reset_token_expiry' => 'datetime',
        'password' => 'hashed',
    ];

    public function driversAdded()
    {
        return $this->hasMany(\App\Models\Driver::class, 'added_by');
    }
}
