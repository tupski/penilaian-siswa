@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-chart-line text-info"></i> 
                Rekap Absensi: {{ $siswa->nama_siswa }}
            </h2>
            <p class="text-muted">
                <i class="fas fa-id-card"></i> NIS: {{ $siswa->nis }} | 
                <i class="fas fa-users"></i> Kelas: {{ $siswa->kelas }}
            </p>
        </div>
        <div class="col-md-4 text-md-right">
            <div class="btn-group btn-group-sm" role="group">
                {{-- Tombol Input Absensi untuk ADMIN dan GURU --}}
                @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isGuru()))
                <a href="{{ route('absensi.create', $siswa->id) }}" class="btn btn-primary btn-sm" style="background: #1e3c72; border-color: #1e3c72;">
                    <i class="fas fa-plus"></i> Input Absensi
                </a>
                @endif
                <a href="{{ route('absensi.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            {{-- Tombol Hapus Semua HANYA UNTUK ADMIN --}}
            @if(auth()->user() && auth()->user()->isAdmin())
            <div class="mt-2">
                <form action="{{ route('absensi.destroy-all', $siswa->id) }}" method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus SEMUA data absensi untuk siswa {{ $siswa->nama_siswa }}? Tindakan ini tidak dapat dibatalkan.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-stats bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">HADIR</h6>
                            <h2 class="mb-0">{{ $rekap['hadir'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-stats bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">SAKIT</h6>
                            <h2 class="mb-0">{{ $rekap['sakit'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-thermometer-half fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-stats bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">IZIN</h6>
                            <h2 class="mb-0">{{ $rekap['izin'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card card-stats bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">ALPA</h6>
                            <h2 class="mb-0">{{ $rekap['alpa'] ?? 0 }}</h2>
                        </div>
                        <i class="fas fa-times-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Kehadiran -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Total Data Absensi:</strong>
                                <span class="badge badge-primary badge-pill px-3 py-2">
                                    {{ $rekap['total_absensi'] ?? 0 }} hari
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total Hadir:</strong>
                                <span class="badge badge-success badge-pill px-3 py-2">
                                    {{ $rekap['hadir'] ?? 0 }} hari
                                </span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <strong>Persentase Kehadiran:</strong>
                            <div class="mt-2">
                                @php
                                    $persen = (int)($rekap['persentase'] ?? 0);
                                    $badgeClass = $persen >= 90 ? 'success' : ($persen >= 75 ? 'warning' : 'danger');
                                @endphp
                                <div class="progress" style="height: 30px;">
                                    <div class="progress-bar bg-{{ $badgeClass }} progress-bar-striped progress-bar-animated" 
                                        role="progressbar" 
                                        style="width: <?= $persen ?>%;" 
                                        aria-valuenow="<?= $persen ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                        <strong><?= $persen ?>%</strong>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    @if($persen >= 90)
                                        <span class="badge badge-success"><i class="fas fa-trophy"></i> Sangat Baik!</span>
                                    @elseif($persen >= 75)
                                        <span class="badge badge-warning"><i class="fas fa-smile"></i> Cukup Baik</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fas fa-frown"></i> Perlu Perhatian</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Absensi -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Detail Absensi Harian
            </h5>
        </div>
        <div class="card-body">
            <div style="overflow-x: auto; width: 100%;">
                <table class="table table-bordered table-hover" id="absensi-detail-table" style="min-width: 600px; width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 20%;">Tanggal</th>
                            <th style="width: 20%;">Status</th>
                            <th style="width: 45%;">Keterangan</th>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <th class="text-center" style="width: 10%;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $index => $absensiItem)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                {{ \Carbon\Carbon::parse($absensiItem->tanggal)->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($absensiItem->tanggal)->translatedFormat('l') }}</small>
                            </td>
                            <td>
                                @switch($absensiItem->status)
                                    @case('hadir')
                                        <span class="badge badge-success badge-pill px-3 py-2">
                                            <i class="fas fa-check-circle"></i> Hadir
                                        </span>
                                        @break
                                    @case('sakit')
                                        <span class="badge badge-warning badge-pill px-3 py-2">
                                            <i class="fas fa-thermometer-half"></i> Sakit
                                        </span>
                                        @break
                                    @case('izin')
                                        <span class="badge badge-info badge-pill px-3 py-2">
                                            <i class="fas fa-file-alt"></i> Izin
                                        </span>
                                        @break
                                    @case('alpa')
                                        <span class="badge badge-danger badge-pill px-3 py-2">
                                            <i class="fas fa-times-circle"></i> Alpa
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $absensiItem->keterangan ?? '-' }}</td>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('absensi.edit', $absensiItem->id) }}" 
                                    class="btn btn-primary" 
                                    title="Edit"
                                    style="background: #1e3c72; border-color: #1e3c72;"
                                    data-toggle="tooltip">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('absensi.destroy', $absensiItem->id) }}" 
                                        method="POST" 
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger" 
                                                title="Hapus"
                                                data-toggle="tooltip"
                                                onclick="return confirm('Yakin ingin menghapus data absensi ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                            <tr>
                                @if(auth()->user() && auth()->user()->isAdmin())
                                <td colspan="5" class="text-center py-4">
                                @else
                                <td colspan="4" class="text-center py-4">
                                @endif
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">Belum ada data absensi</h5>
                                    @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isGuru()))
                                    <a href="{{ route('absensi.create', $siswa->id) }}" class="btn btn-success mt-2">
                                        <i class="fas fa-plus"></i> Input Absensi Sekarang
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    
    var table = $('#absensi-detail-table');
    if (table.length && !$.fn.DataTable.isDataTable(table)) {
        table.DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
            },
            "paging": false,
            "ordering": true,
            "info": true,
            "searching": true,
            "order": [[1, 'desc']],
            "responsive": false,
            "autoWidth": false,
            "scrollX": true,
            "scrollY": "400px",
            "scrollCollapse": true
        });
    }
});
</script>
@endpush