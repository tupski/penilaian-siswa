<?php
// app/Http/Controllers/AbsensiController.php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use App\Models\Absensi;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index()
    {
        $siswas = Alternatif::with('absensis')->get();
        
        return view('absensi.index', compact('siswas'));
    }

    public function create($siswaId = null)
    {
        $siswas = Alternatif::all();
        $selectedSiswa = $siswaId ? Alternatif::find($siswaId) : null;
        $tanggal = Carbon::now()->format('Y-m-d');
        
        return view('absensi.create', compact('siswas', 'selectedSiswa', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'alternatif_id' => 'required|exists:alternatifs,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpa',
            'keterangan' => 'nullable|string'
        ]);

        $existing = Absensi::where('alternatif_id', $request->alternatif_id)
                           ->where('tanggal', $request->tanggal)
                           ->first();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Absensi untuk siswa ini pada tanggal tersebut sudah ada. Silakan edit jika perlu.');
        }

        Absensi::create([
            'alternatif_id' => $request->alternatif_id,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        $this->syncNilaiKehadiran($request->alternatif_id);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil disimpan');
    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $siswas = Alternatif::all();
        
        return view('absensi.edit', compact('absensi', 'siswas'));
    }

    public function update(Request $request, $id)
    {
        $absensi = Absensi::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:hadir,sakit,izin,alpa',
            'keterangan' => 'nullable|string'
        ]);

        $absensi->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan
        ]);

        $this->syncNilaiKehadiran($absensi->alternatif_id);

        return redirect()->route('absensi.rekap', $absensi->alternatif_id)->with('success', 'Data absensi berhasil diupdate');
    }

    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $siswaId = $absensi->alternatif_id;
        $absensi->delete();
        
        $this->syncNilaiKehadiran($siswaId);

        return redirect()->route('absensi.rekap', $siswaId)->with('success', 'Data absensi berhasil dihapus');
    }

    public function destroyAll($siswaId)
    {
        $deleted = Absensi::where('alternatif_id', $siswaId)->delete();
        
        $this->syncNilaiKehadiran($siswaId);

        return redirect()->route('absensi.rekap', $siswaId)->with('success', "$deleted data absensi berhasil dihapus");
    }

    /**
     * Hapus semua data absensi (reset seluruh data absensi)
     */
    public function destroyAllAbsensi()
    {
        $total = Absensi::count();
        Absensi::truncate();
        
        $this->syncNilaiKehadiran();
        
        return redirect()->route('absensi.index')->with('success', "Berhasil menghapus semua ($total) data absensi!");
    }
    
    public function rekap($siswaId)
    {
        $siswa = Alternatif::findOrFail($siswaId);
        $absensis = $siswa->absensis()->orderBy('tanggal', 'desc')->get();
        $rekap = Absensi::getRekap($siswaId);
        
        return view('absensi.rekap', compact('siswa', 'absensis', 'rekap'));
    }
    
    public function syncNilaiKehadiran($siswaId = null)
    {
        $kriteriaKehadiran = Kriteria::where('nama_kriteria', 'Kehadiran')->first();
        
        if (!$kriteriaKehadiran) {
            if (!$siswaId) {
                return redirect()->route('absensi.index')->with('error', 'Kriteria Kehadiran tidak ditemukan');
            }
            return;
        }
        
        if ($siswaId) {
            $siswas = Alternatif::where('id', $siswaId)->get();
        } else {
            $siswas = Alternatif::all();
        }
        
        foreach ($siswas as $siswa) {
            $nilaiKehadiran = $siswa->nilai_kehadiran;
            
            Penilaian::updateOrCreate(
                [
                    'alternatif_id' => $siswa->id,
                    'kriteria_id' => $kriteriaKehadiran->id
                ],
                ['nilai' => $nilaiKehadiran]
            );
        }
        
        if (!$siswaId) {
            return redirect()->route('absensi.index')->with('success', 'Nilai kehadiran semua siswa berhasil disinkronisasi');
        }
    }
    
    public function syncAll()
    {
        return $this->syncNilaiKehadiran();
    }
    
    public function massCreate()
    {
        $siswas = Alternatif::all();
        $tanggal = Carbon::now()->format('Y-m-d');
        $existingAbsensi = Absensi::where('tanggal', $tanggal)->pluck('status', 'alternatif_id')->toArray();
        
        return view('absensi.mass', compact('siswas', 'tanggal', 'existingAbsensi'));
    }
    
    public function massStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'absensi' => 'required|array',
            'absensi.*' => 'required|in:hadir,sakit,izin,alpa'
        ]);
        
        foreach ($request->absensi as $siswaId => $status) {
            if (!empty($status)) {
                Absensi::updateOrCreate(
                    [
                        'alternatif_id' => $siswaId,
                        'tanggal' => $request->tanggal
                    ],
                    ['status' => $status]
                );
            }
        }
        
        $this->syncNilaiKehadiran();
        
        return redirect()->route('absensi.index')->with('success', 'Absensi massal berhasil disimpan');
    }

    public function rekapBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        $siswas = Alternatif::all();
        $rekapBulanan = [];
        
        foreach ($siswas as $siswa) {
            $absensis = $siswa->absensis()
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();
            
            $hadir = $absensis->where('status', 'hadir')->count();
            $sakit = $absensis->where('status', 'sakit')->count();
            $izin = $absensis->where('status', 'izin')->count();
            $alpa = $absensis->where('status', 'alpa')->count();
            $total = $absensis->count();
            $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;
            
            $rekapBulanan[] = [
                'nama' => $siswa->nama_siswa,
                'nis' => $siswa->nis,
                'kelas' => $siswa->kelas,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'persen' => $persen
            ];
        }
        
        usort($rekapBulanan, function($a, $b) {
            return $b['persen'] <=> $a['persen'];
        });
        
        return view('absensi.rekap_bulanan', compact('rekapBulanan', 'bulan', 'tahun'));
    }
}