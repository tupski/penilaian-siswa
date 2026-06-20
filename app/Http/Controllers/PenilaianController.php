<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\SubKriteria;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PenilaianController extends Controller
{
    public function index()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        $penilaians = Penilaian::with(['alternatif', 'kriteria'])->get();
        
        return view('penilaian.index', compact('alternatifs', 'kriterias', 'penilaians'));
    }

    public function create()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::with('subKriterias')->get();
        
        return view('penilaian.create', compact('alternatifs', 'kriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alternatif_id' => 'required',
            'nilai' => 'required|array'
        ]);

        foreach ($request->nilai as $kriteria_id => $nilai) {
            // Skip jika kriteria Kehadiran (otomatis dari absensi)
            $kriteria = Kriteria::find($kriteria_id);
            if ($kriteria && $kriteria->nama_kriteria == 'Kehadiran') {
                continue;
            }
            
            Penilaian::updateOrCreate(
                [
                    'alternatif_id' => $request->alternatif_id,
                    'kriteria_id' => $kriteria_id
                ],
                ['nilai' => $nilai]
            );
        }

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil disimpan');
    }

    public function edit($alternatif_id)
    {
        $alternatif = Alternatif::findOrFail($alternatif_id);
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        $penilaians = Penilaian::where('alternatif_id', $alternatif_id)->get()->keyBy('kriteria_id');
        
        // Ambil nilai kehadiran dari absensi
        $nilaiKehadiran = $alternatif->nilai_kehadiran;
        
        return view('penilaian.edit', compact('alternatif', 'alternatifs', 'kriterias', 'penilaians', 'nilaiKehadiran'));
    }

    public function update(Request $request, $alternatif_id)
    {
        $request->validate([
            'nilai' => 'required|array'
        ]);

        foreach ($request->nilai as $kriteria_id => $nilai) {
            // Skip jika kriteria Kehadiran (otomatis dari absensi)
            $kriteria = Kriteria::find($kriteria_id);
            if ($kriteria && $kriteria->nama_kriteria == 'Kehadiran') {
                continue;
            }
            
            Penilaian::updateOrCreate(
                [
                    'alternatif_id' => $alternatif_id,
                    'kriteria_id' => $kriteria_id
                ],
                ['nilai' => $nilai]
            );
        }

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil diupdate');
    }

    public function destroy($alternatif_id, $kriteria_id)
    {
        Penilaian::where('alternatif_id', $alternatif_id)
                 ->where('kriteria_id', $kriteria_id)
                 ->delete();
        
        return back()->with('success', 'Penilaian berhasil dihapus');
    }

    /**
     * Sinkronisasi nilai kehadiran dari absensi ke penilaian
     */
    public function syncKehadiran()
    {
        $count = Penilaian::syncKehadiranForAllSiswa();
        
        if ($count === false) {
            return redirect()->route('penilaian.index')->with('error', 'Kriteria Kehadiran tidak ditemukan');
        }
        
        return redirect()->route('penilaian.index')->with('success', "{$count} data nilai kehadiran berhasil disinkronisasi dari absensi");
    }

    /**
     * Export ke PDF
     */
    public function exportPDF()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        
        // Kumpulkan data penilaian
        $data = [];
        foreach ($alternatifs as $alternatif) {
            $row = [
                'nis' => $alternatif->nis,
                'nama' => $alternatif->nama_siswa,
                'kelas' => $alternatif->kelas,
            ];
            
            foreach ($kriterias as $kriteria) {
                $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
                                      ->where('kriteria_id', $kriteria->id)
                                      ->first();
                $row[$kriteria->nama_kriteria] = $penilaian ? $penilaian->nilai : 0;
            }
            
            $data[] = $row;
        }
        
        $kepalaSekolah = 'H. MUNSARI, M.Pd';
        $tanggalCetak = date('d-m-Y H:i:s');
        
        $pdf = Pdf::loadView('penilaian.export-pdf', compact('data', 'kriterias', 'kepalaSekolah', 'tanggalCetak'));
        return $pdf->download('data-penilaian-siswa-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export ke CSV (bisa dibuka di Excel) - Dengan perbaikan NIS
     */
    public function exportCSV()
    {
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        
        $filename = 'data-penilaian-siswa-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($alternatifs, $kriterias) {
            $handle = fopen('php://output', 'w');
            
            // BOM untuk UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            $headers = ['No', 'NIS', 'Nama Siswa', 'Kelas'];
            foreach ($kriterias as $kriteria) {
                $headers[] = $kriteria->nama_kriteria;
            }
            fputcsv($handle, $headers);
            
            // Data
            $no = 1;
            foreach ($alternatifs as $alternatif) {
                // PERBAIKAN: Format NIS agar tidak berubah jadi eksponensial di Excel
                // Dengan menambahkan =" " agar terbaca sebagai teks
                $nis = '="' . $alternatif->nis . '"';
                
                $row = [$no++, $nis, $alternatif->nama_siswa, $alternatif->kelas];
                
                foreach ($kriterias as $kriteria) {
                    $penilaian = Penilaian::where('alternatif_id', $alternatif->id)
                                          ->where('kriteria_id', $kriteria->id)
                                          ->first();
                    $row[] = $penilaian ? $penilaian->nilai : 0;
                }
                fputcsv($handle, $row);
            }
            
            // Footer
            fputcsv($handle, []);
            fputcsv($handle, ['Tangerang, ' . date('d F Y')]);
            fputcsv($handle, ['Kepala Madrasah,']);
            fputcsv($handle, []);
            fputcsv($handle, ['H. MUNSARI, M.Pd']);
            fputcsv($handle, ['NIP. 196904201999031002']);
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}