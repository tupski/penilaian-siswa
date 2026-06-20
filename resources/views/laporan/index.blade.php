@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2>Laporan Penilaian</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                <h4>Cetak Laporan Penilaian Siswa</h4>
                <p>Klik tombol di bawah untuk mencetak laporan dalam format PDF</p>
                <a href="{{ route('laporan.cetak-pdf') }}" class="btn btn-danger btn-lg" target="_blank">
                    <i class="fas fa-print"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection