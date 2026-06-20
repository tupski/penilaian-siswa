<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@spk.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        User::create([
            'name' => 'Guru',
            'email' => 'guru@spk.com',
            'password' => Hash::make('guru123'),
            'role' => 'guru'
        ]);
    }
}