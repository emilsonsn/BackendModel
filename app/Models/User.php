<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Notification;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $visible = [
        'id',
        'name',
        'surname',
        'email',
        'email_verified_at',
        'group',
        'image',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'surname',
        'email',
        'email_verified_at',
        'group',
        'image',
        'remember_token',
        'password',
    ];

    protected $hidden = [
        'password'
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class);
    }
}
