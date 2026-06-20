<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class RangkingController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::all();
        
        // Normalisasi bobot kriteria
        $totalBobot = $kriterias->sum('bobot');
        foreach ($kriterias as $kriteria) {
            $kriteria->bobot_normalisasi = $kriteria->bobot / $totalBobot;
        }
        
        $results = [];
        foreach ($alternatifs as $alternatif) {
            $totalNilai = 0;
            $details = [];
            
            foreach ($kriterias as $kriteria) {
                // Ambil nilai dari tabel penilaian
                $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
                                      ->where('kriteria_id', $kriteria->id)
                                      ->first();
                
                $nilai = $penilaian ? $penilaian->nilai : 0;
                
                // Normalisasi nilai (asumsi nilai max 100)
                $nilai_normalisasi = $nilai / 100;
                
                // Hitung nilai utility
                $utility = $nilai_normalisasi * $kriteria->bobot_normalisasi;
                
                $details[] = [
                    'kriteria' => $kriteria->nama_kriteria,
                    'kode' => $kriteria->kode_kriteria,
                    'nilai' => $nilai,
                    'bobot' => $kriteria->bobot,
                    'utility' => $utility
                ];
                
                $totalNilai += $utility;
            }
            
            $results[] = [
                'alternatif' => $alternatif,
                'total_nilai' => $totalNilai * 100,
                'details' => $details
            ];
        }
        
        // Urutkan berdasarkan nilai tertinggi
        usort($results, function($a, $b) {
            return $b['total_nilai'] <=> $a['total_nilai'];
        });
        
        foreach ($results as $index => $result) {
            $results[$index]['rangking'] = $index + 1;
        }
        
        return view('rangking.index', compact('results', 'kriterias'));
    }
}