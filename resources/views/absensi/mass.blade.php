@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>
                <i class="fas fa-layer-group"></i> 
                Input Absensi Massal
            </h2>
            <p class="text-muted">Input absensi untuk semua siswa dalam satu tanggal</p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('absensi.mass-store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" required>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswas as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->kelas }}</td>
                                <td>
                                    <select name="absensi[{{ $siswa->id }}]" class="form-control" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="hadir" {{ isset($existingAbsensi[$siswa->id]) && $existingAbsensi[$siswa->id] == 'hadir' ? 'selected' : '' }}>✅ Hadir</option>
                                        <option value="sakit" {{ isset($existingAbsensi[$siswa->id]) && $existingAbsensi[$siswa->id] == 'sakit' ? 'selected' : '' }}>🤒 Sakit</option>
                                        <option value="izin" {{ isset($existingAbsensi[$siswa->id]) && $existingAbsensi[$siswa->id] == 'izin' ? 'selected' : '' }}>📝 Izin</option>
                                        <option value="alpa" {{ isset($existingAbsensi[$siswa->id]) && $existingAbsensi[$siswa->id] == 'alpa' ? 'selected' : '' }}>❌ Alpa</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection