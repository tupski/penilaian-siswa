<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah data yang sudah ada dari 'user' menjadi 'guru'
        DB::table('users')->where('role', 'user')->update(['role' => 'guru']);
        
        // Ubah enum di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru'])->default('guru')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->change();
        });
        
        DB::table('users')->where('role', 'guru')->update(['role' => 'user']);
    }
};