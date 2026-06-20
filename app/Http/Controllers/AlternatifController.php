<?php

namespace App\Http\Controllers;

use App\Models\Alternatif;
use Illuminate\Http\Request;

class AlternatifController extends Controller
{
    public function index()
    {
        $alternatifs = Alternatif::orderBy('id', 'desc')->get();
        return view('alternatif.index', compact('alternatifs'));
    }

    public function create()
    {
        return view('alternatif.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:alternatifs',
            'nama_siswa' => 'required',
            'kelas' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        Alternatif::create($request->all());
        return redirect()->route('alternatif.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function edit(Alternatif $alternatif)
    {
        return view('alternatif.edit', compact('alternatif'));
    }

    public function update(Request $request, Alternatif $alternatif)
    {
        $request->validate([
            'nis' => 'required|unique:alternatifs,nis,' . $alternatif->id,
            'nama_siswa' => 'required',
            'kelas' => 'required',
            'jenis_kelamin' => 'required'
        ]);

        $alternatif->update($request->all());
        return redirect()->route('alternatif.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy(Alternatif $alternatif)
    {
        $alternatif->delete();
        return redirect()->route('alternatif.index')->with('success', 'Data siswa berhasil dihapus');
    }
}