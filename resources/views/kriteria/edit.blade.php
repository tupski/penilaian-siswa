@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Edit Kriteria</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('kriteria.update', $kriteria->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" value="{{ $kriteria->kode_kriteria }}" required>
                </div>
                <div class="form-group">
                    <label>Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" value="{{ $kriteria->nama_kriteria }}" required>
                </div>
                <div class="form-group">
                    <label>Bobot (%)</label>
                    <input type="number" name="bobot" class="form-control" step="0.01" min="0" max="100" value="{{ $kriteria->bobot }}" required>
                </div>
                <div class="form-group">
                    <label>Jenis</label>
                    <select name="jenis" class="form-control" required>
                        <option value="">Pilih</option>
                        <option value="benefit" {{ $kriteria->jenis == 'benefit' ? 'selected' : '' }}>Benefit (Semakin besar semakin baik)</option>
                        <option value="cost" {{ $kriteria->jenis == 'cost' ? 'selected' : '' }}>Cost (Semakin kecil semakin baik)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection