<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Method role checking
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isGuru()
    {
        return $this->role === 'guru';
    }
    
    // Untuk kompatibilitas dengan code lama yang pakai isUser()
    public function isUser()
    {
        return $this->role === 'guru';
    }
}