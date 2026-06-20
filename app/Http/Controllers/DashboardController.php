<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Alternatif::count();
        $totalKriteria = Kriteria::count();
        $totalPenilaian = Penilaian::count();
        
        return view('dashboard', compact('totalSiswa', 'totalKriteria', 'totalPenilaian'));
    }
}