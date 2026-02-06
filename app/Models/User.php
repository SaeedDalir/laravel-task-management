<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Models\Methods\UserMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, UserMethod;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'failed_logins',
        'locked_until',
        'last_failure',
        'last_logged_in',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'email' => 'string',
            'password' => 'hashed',
            'role' => RoleEnum::class,
            'failed_logins' => 'array',
            'locked_until' => 'datetime',
            'last_failure' => 'datetime',
            'last_logged_in' => 'datetime',
        ];
    }
}
