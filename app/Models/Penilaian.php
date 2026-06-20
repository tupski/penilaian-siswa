<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'alternatif_id', 'kriteria_id', 'nilai'
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Sinkronisasi nilai kehadiran dari absensi untuk semua siswa
     */
    public static function syncKehadiranForAllSiswa()
    {
        $kriteriaKehadiran = Kriteria::where('nama_kriteria', 'Kehadiran')->first();
        
        if (!$kriteriaKehadiran) {
            return false;
        }
        
        $siswas = Alternatif::all();
        $count = 0;
        
        foreach ($siswas as $siswa) {
            $nilaiKehadiran = $siswa->nilai_kehadiran;
            
            self::updateOrCreate(
                [
                    'alternatif_id' => $siswa->id,
                    'kriteria_id' => $kriteriaKehadiran->id
                ],
                ['nilai' => $nilaiKehadiran]
            );
            $count++;
        }
        
        return $count;
    }
}