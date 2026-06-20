@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>
                <i class="fas fa-calendar-plus text-primary"></i> 
                Input Absensi Siswa
            </h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('absensi.store') }}" method="POST">
                @csrf
                
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        <i class="fas fa-user"></i> Pilih Siswa <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-9">
                        <select name="alternatif_id" class="form-control select2" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}" {{ $selectedSiswa && $selectedSiswa->id == $siswa->id ? 'selected' : '' }}>
                                {{ $siswa->nis }} - {{ $siswa->nama_siswa }} ({{ $siswa->kelas }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        <i class="fas fa-calendar"></i> Tanggal <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-9">
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal ?? date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        <i class="fas fa-flag-checkered"></i> Status Kehadiran <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="hadir" id="status_hadir" class="custom-control-input" required>
                                    <label class="custom-control-label text-success" for="status_hadir">
                                        <i class="fas fa-check-circle"></i> Hadir
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="sakit" id="status_sakit" class="custom-control-input">
                                    <label class="custom-control-label text-warning" for="status_sakit">
                                        <i class="fas fa-thermometer-half"></i> Sakit
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="izin" id="status_izin" class="custom-control-input">
                                    <label class="custom-control-label text-info" for="status_izin">
                                        <i class="fas fa-file-alt"></i> Izin
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="status" value="alpa" id="status_alpa" class="custom-control-input">
                                    <label class="custom-control-label text-danger" for="status_alpa">
                                        <i class="fas fa-times-circle"></i> Alpa
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-form-label">
                        <i class="fas fa-comment"></i> Keterangan
                    </label>
                    <div class="col-md-9">
                        <textarea name="keterangan" class="form-control" rows="3" 
                                  placeholder="Isi keterangan jika diperlukan (contoh: sakit flu, izin keluarga, dll)..."></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('absensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih Siswa --',
        allowClear: true
    });
});
</script>
@endpush