@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Tambah Kriteria</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('kriteria.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Pilih Nama Kriteria</label>
                    <select name="selected_kriteria" id="selected_kriteria" class="form-control" required>
                        <option value="">-- Pilih Kriteria --</option>
                        <option value="Akademik" data-bobot="40">Akademik (Bobot Default: 40%)</option>
                        <option value="Kehadiran" data-bobot="25">Kehadiran (Bobot Default: 25%)</option>
                        <option value="Sikap" data-bobot="20">Sikap (Bobot Default: 20%)</option>
                        <option value="Pengetahuan Agama" data-bobot="15">Pengetahuan Agama (Bobot Default: 15%)</option>
                        <option value="custom">-- Custom / Buat Kriteria Baru --</option>
                    </select>
                </div>

                <div class="form-group" id="custom_nama_group" style="display: none;">
                    <label>Nama Kriteria Custom</label>
                    <input type="text" name="custom_nama" class="form-control" placeholder="Masukkan nama kriteria">
                </div>

                <div class="form-group">
                    <label>Kode Kriteria</label>
                    <input type="text" name="kode_kriteria" class="form-control" placeholder="Contoh: C1" required>
                    <small class="text-muted">Contoh: C1, C2, C3, dst</small>
                </div>

                <div class="form-group">
                    <label>Bobot (%)</label>
                    <input type="number" name="bobot" id="bobot" class="form-control" step="0.01" min="0" max="100" required>
                    <small class="text-muted">Bobot bisa diubah sesuai kebutuhan</small>
                </div>

                <div class="form-group">
                    <label>Jenis</label>
                    <select name="jenis" class="form-control" required>
                        <option value="benefit">Benefit (Semakin besar semakin baik)</option>
                        <option value="cost">Cost (Semakin kecil semakin baik)</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#selected_kriteria').change(function() {
            var selected = $(this).find(':selected');
            var bobot = selected.data('bobot');
            
            if ($(this).val() == 'custom') {
                $('#custom_nama_group').show();
                $('#bobot').val('');
                $('#bobot').prop('required', false);
            } else {
                $('#custom_nama_group').hide();
                if (bobot) {
                    $('#bobot').val(bobot);
                }
                $('#bobot').prop('required', true);
            }
        });
    });
</script>
@endpush
@endsection