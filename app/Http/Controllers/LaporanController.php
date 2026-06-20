<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }
    
    public function cetakPDF()
    {
        $kriterias = Kriteria::all();
        $alternatifs = Alternatif::all();
        
        // Hitung normalisasi bobot
        $totalBobot = $kriterias->sum('bobot');
        foreach ($kriterias as $kriteria) {
            $kriteria->bobot_normalisasi = $kriteria->bobot / $totalBobot;
        }
        
        // Hitung nilai untuk setiap alternatif
        $results = [];
        foreach ($alternatifs as $alternatif) {
            $totalNilai = 0;
            
            foreach ($kriterias as $kriteria) {
                $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
                                      ->where('kriteria_id', $kriteria->id)
                                      ->first();
                
                $nilai = $penilaian ? $penilaian->nilai : 0;
                $nilai_normalisasi = $nilai / 100;
                $utility = $nilai_normalisasi * $kriteria->bobot_normalisasi;
                $totalNilai += $utility;
            }
            
            $results[] = [
                'alternatif' => $alternatif,
                'total_nilai' => $totalNilai * 100
            ];
        }
        
        // Urutkan dan beri rangking
        usort($results, function($a, $b) {
            return $b['total_nilai'] <=> $a['total_nilai'];
        });
        
        foreach ($results as $index => $result) {
            $results[$index]['rangking'] = $index + 1;
        }
        
        $pdf = Pdf::loadView('laporan.pdf', compact('results', 'kriterias'));
        return $pdf->download('laporan-penilaian-siswa.pdf');
    }
}