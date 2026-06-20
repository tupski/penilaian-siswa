@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Tambah Data Siswa</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('alternatif.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>NIS</label>
                    <input type="text" name="nis" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nama Siswa</label>
                    <input type="text" name="nama_siswa" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Kelas</label>
                    <input type="text" name="kelas" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">Pilih</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('alternatif.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection