@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>
                <i class="fas fa-edit"></i> 
                Edit Absensi
            </h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Siswa</label>
                    <input type="text" class="form-control" value="{{ $absensi->alternatif->nama_siswa }} ({{ $absensi->alternatif->nis }})" disabled>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}" disabled>
                </div>

                <div class="form-group">
                    <label>Status Kehadiran</label>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" value="hadir" id="status_hadir" class="custom-control-input" 
                                    {{ $absensi->status == 'hadir' ? 'checked' : '' }} required>
                                <label class="custom-control-label text-success" for="status_hadir">
                                    <i class="fas fa-check-circle"></i> Hadir
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" value="sakit" id="status_sakit" class="custom-control-input"
                                    {{ $absensi->status == 'sakit' ? 'checked' : '' }}>
                                <label class="custom-control-label text-warning" for="status_sakit">
                                    <i class="fas fa-thermometer-half"></i> Sakit
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" value="izin" id="status_izin" class="custom-control-input"
                                    {{ $absensi->status == 'izin' ? 'checked' : '' }}>
                                <label class="custom-control-label text-info" for="status_izin">
                                    <i class="fas fa-file-alt"></i> Izin
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" value="alpa" id="status_alpa" class="custom-control-input"
                                    {{ $absensi->status == 'alpa' ? 'checked' : '' }}>
                                <label class="custom-control-label text-danger" for="status_alpa">
                                    <i class="fas fa-times-circle"></i> Alpa
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ $absensi->keterangan }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <a href="{{ route('absensi.rekap', $absensi->alternatif_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection