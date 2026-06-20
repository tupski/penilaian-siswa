@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Edit Penilaian: {{ $alternatif->nama_siswa }}</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('penilaian.update', $alternatif->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <h4 class="mt-4">Nilai per Kriteria</h4>
                <hr>
                
                @foreach($kriterias as $kriteria)
                <div class="form-group">
                    <label>{{ $kriteria->nama_kriteria }} (Bobot: {{ $kriteria->bobot }}%)</label>
                    <input type="number" name="nilai[{{ $kriteria->id }}]" class="form-control" 
                           min="0" max="100" step="1" required 
                           value="{{ isset($penilaians[$kriteria->id]) ? $penilaians[$kriteria->id]->nilai : '' }}"
                           placeholder="Masukkan nilai 0-100">
                    <small class="text-muted">Nilai maksimal 100</small>
                </div>
                @endforeach
                
                <button type="submit" class="btn btn-primary">Update Penilaian</button>
                <a href="{{ route('penilaian.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection