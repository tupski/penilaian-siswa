@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12">
            <h2 class="mb-4">Dashboard</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4 mb-4">
            <div class="card card-stats bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Siswa</h6>
                            <h2 class="mb-0">{{ $totalSiswa ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 mb-4">
            <div class="card card-stats bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Kriteria</h6>
                            <h2 class="mb-0">{{ $totalKriteria ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 mb-4">
            <div class="card card-stats bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total Penilaian</h6>
                            <h2 class="mb-0">{{ $totalPenilaian ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-star fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Selamat Datang di Sistem Penilaian Siswa</h5>
                </div>
                <div class="card-body">
                    <p>Sistem ini menggunakan metode <strong>SMART (Simple Multi Attribute Rating Technique)</strong> untuk menentukan siswa terbaik.</p>
                    <p>Kriteria penilaian:</p>
                    <ul>
                        <li>Akademik (40%)</li>
                        <li>Kehadiran (25%)</li>
                        <li>Sikap (20%)</li>
                        <li>Pengetahuan Agama (15%)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
