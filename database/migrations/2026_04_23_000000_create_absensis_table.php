<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatifs')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['alternatif_id', 'tanggal']);
            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};