<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternatifs', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('nama_siswa', 100);
            $table->string('kelas', 20);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternatifs');
    }
};