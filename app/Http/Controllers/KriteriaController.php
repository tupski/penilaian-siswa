<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::with('subKriterias')->orderBy('kode_kriteria')->get();
        return view('kriteria.index', compact('kriterias'));
    }

    public function create()
    {
        return view('kriteria.create');
    }

    public function store(Request $request)
    {
        // Validasi untuk create (dengan pilihan)
        $request->validate([
            'selected_kriteria' => 'required',
            'kode_kriteria' => 'required|unique:kriterias',
            'bobot' => 'required|numeric|min:0|max:100',
            'jenis' => 'required|in:benefit,cost'
        ]);

        // Tentukan nama kriteria
        if ($request->selected_kriteria == 'custom') {
            $request->validate([
                'custom_nama' => 'required|string'
            ]);
            $nama_kriteria = $request->custom_nama;
        } else {
            $nama_kriteria = $request->selected_kriteria;
        }

        Kriteria::create([
            'kode_kriteria' => $request->kode_kriteria,
            'nama_kriteria' => $nama_kriteria,
            'bobot' => $request->bobot,
            'jenis' => $request->jenis
        ]);

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil ditambahkan');
    }

    public function show($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.show', compact('kriteria'));
    }

    public function edit($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        return view('kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);
        
        // Validasi untuk update (tanpa selected_kriteria)
        $request->validate([
            'kode_kriteria' => 'required|unique:kriterias,kode_kriteria,' . $id,
            'nama_kriteria' => 'required|string',
            'bobot' => 'required|numeric|min:0|max:100',
            'jenis' => 'required|in:benefit,cost'
        ]);

        $kriteria->update([
            'kode_kriteria' => $request->kode_kriteria,
            'nama_kriteria' => $request->nama_kriteria,
            'bobot' => $request->bobot,
            'jenis' => $request->jenis
        ]);

        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil diupdate');
    }

    public function destroy($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        
        // Hapus sub kriteria terlebih dahulu
        $kriteria->subKriterias()->delete();
        
        // Hapus penilaian yang terkait
        $kriteria->penilaians()->delete();
        
        // Hapus kriteria
        $kriteria->delete();
        
        return redirect()->route('kriteria.index')->with('success', 'Kriteria berhasil dihapus');
    }

    public function subKriteria($id)
    {
        $kriteria = Kriteria::findOrFail($id);
        $subKriterias = SubKriteria::where('kriteria_id', $id)->get();
        return view('kriteria.sub', compact('kriteria', 'subKriterias'));
    }

    public function storeSubKriteria(Request $request, $id)
    {
        $request->validate([
            'nama_sub' => 'required|string',
            'nilai' => 'required|integer|min:0|max:100'
        ]);

        SubKriteria::create([
            'kriteria_id' => $id,
            'nama_sub' => $request->nama_sub,
            'nilai' => $request->nilai
        ]);

        return redirect()->route('kriteria.sub', $id)->with('success', 'Sub kriteria berhasil ditambahkan');
    }

    public function destroySubKriteria($id)
    {
        $sub = SubKriteria::findOrFail($id);
        $kriteria_id = $sub->kriteria_id;
        $sub->delete();
        
        return redirect()->route('kriteria.sub', $kriteria_id)->with('success', 'Sub kriteria berhasil dihapus');
    }
}