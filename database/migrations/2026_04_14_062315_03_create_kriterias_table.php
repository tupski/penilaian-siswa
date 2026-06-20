<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kriteria', 10)->unique();
            $table->string('nama_kriteria', 50);
            $table->decimal('bobot', 5, 2);
            $table->enum('jenis', ['benefit', 'cost']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};