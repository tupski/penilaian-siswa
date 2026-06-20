<?php
// app/Models/Alternatif.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis', 'nama_siswa', 'kelas', 'jenis_kelamin'
    ];

    public function penilaians()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Hitung nilai kehadiran (persentase) - MAKSIMAL 100
     */
    public function getNilaiKehadiranAttribute()
    {
        $hadir = $this->absensis()->where('status', 'hadir')->count();
        $total = $this->absensis()->count();
        
        if ($total == 0) {
            return 0;
        }
        
        // Hitung persentase dan batasi maksimal 100
        $persentase = round(($hadir / $total) * 100);
        return min($persentase, 100);
    }
    
    /**
     * Get detail kehadiran untuk rekap
     */
    public function getDetailKehadiranAttribute()
    {
        $absensis = $this->absensis()->get();
        
        $hadir = $absensis->where('status', 'hadir')->count();
        $sakit = $absensis->where('status', 'sakit')->count();
        $izin = $absensis->where('status', 'izin')->count();
        $alpa = $absensis->where('status', 'alpa')->count();
        $total = $absensis->count();
        
        // Persentase dibatasi maksimal 100
        $persentase = $total > 0 ? round(($hadir / $total) * 100) : 0;
        $persentase = min($persentase, 100);
        
        return [
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'total_absensi' => $total,
            'persentase' => $persentase,
        ];
    }
}