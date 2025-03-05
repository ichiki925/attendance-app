<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $attributes = [
        'role' => 'user',
    ];


    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function isUser()
    {
        return $this->role === 'user';
    }


    // attendancesテーブルとのリレーション (1対多)
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // attendance_requestsテーブルとのリレーション (1対多)
    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }



    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
