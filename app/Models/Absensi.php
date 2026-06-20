<?php
// app/Models/Absensi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';
    
    protected $fillable = [
        'alternatif_id', 'tanggal', 'status', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }

    /**
     * Hitung persentase kehadiran (maksimal 100%)
     */
    public static function hitungPersentaseKehadiran($siswaId)
    {
        $absensis = self::where('alternatif_id', $siswaId)->get();
        
        if ($absensis->isEmpty()) {
            return 0;
        }
        
        $hadir = $absensis->where('status', 'hadir')->count();
        $total = $absensis->count();
        
        if ($total == 0) {
            return 0;
        }
        
        // Hitung persentase dan batasi maksimal 100
        $persentase = round(($hadir / $total) * 100);
        
        // Pastikan tidak lebih dari 100
        return min($persentase, 100);
    }

    /**
     * Get rekap kehadiran
     */
    public static function getRekap($siswaId)
    {
        $absensis = self::where('alternatif_id', $siswaId)->get();
        
        $hadir = $absensis->where('status', 'hadir')->count();
        $sakit = $absensis->where('status', 'sakit')->count();
        $izin = $absensis->where('status', 'izin')->count();
        $alpa = $absensis->where('status', 'alpa')->count();
        $total = $absensis->count();
        
        // Hitung persentase dan batasi maksimal 100
        if ($total > 0) {
            $persentase = round(($hadir / $total) * 100);
            $persentase = min($persentase, 100);
        } else {
            $persentase = 0;
        }
        
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