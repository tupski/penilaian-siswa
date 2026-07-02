@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <h2 class="mb-2">
                <i class="fas fa-calendar-check text-primary"></i>
                Data Absensi Siswa
            </h2>
            <p class="text-muted small">
                <i class="fas fa-info-circle"></i> Total hari efektif: (Senin-Jumat)
            </p>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            @if(auth()->user() && auth()->user()->isAdmin())
            <div class="d-flex flex-wrap gap-2 justify-content-md-end" style="gap: 5px;">
                <a href="{{ route('absensi.create') }}" class="btn btn-primary" style="background: #1e3c72; border-color: #1e3c72; padding: 5px 12px; font-size: 0.75rem; border-radius: 4px;">
                    <i class="fas fa-plus"></i> Tambah
                </a>
                <a href="{{ route('absensi.mass-create') }}" class="btn btn-primary" style="background: #2a5298; border-color: #2a5298; padding: 5px 12px; font-size: 0.75rem; border-radius: 4px;">
                    <i class="fas fa-layer-group"></i> Massal
                </a>
                <form action="{{ route('absensi.sync') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: #1e3c72; border-color: #1e3c72; padding: 5px 12px; font-size: 0.75rem; border-radius: 4px;" onclick="return confirm('Sinkronisasi akan menghitung ulang semua nilai kehadiran. Lanjutkan?')">
                        <i class="fas fa-sync"></i> Sinkronisasi
                    </button>
                </form>
                <form action="{{ route('absensi.destroy-all-absensi') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 5px 12px; font-size: 0.75rem; border-radius: 4px;" onclick="return confirm('PERINGATAN! Anda akan menghapus SEMUA data absensi. Tindakan ini tidak dapat dibatalkan. Lanjutkan?')">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </button>
                </form>
            </div>
            @endif

            @if(auth()->user() && auth()->user()->isGuru())
            <div class="d-flex flex-wrap gap-2 justify-content-md-end" style="gap: 5px;">
                <a href="{{ route('absensi.create') }}" class="btn btn-primary" style="background: #1e3c72; border-color: #1e3c72; padding: 5px 12px; font-size: 0.75rem; border-radius: 4px;">
                    <i class="fas fa-plus"></i> Tambah Absensi
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="card card-stats bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="card-title mb-0 text-uppercase">Total Siswa</small>
                            <h3 class="mb-0">{{ $siswas->count() }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card card-stats bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="card-title mb-0 text-uppercase">Total Hadir</small>
                            <h3 class="mb-0">{{ $siswas->sum(function($s) { return $s->detail_kehadiran['hadir'] ?? 0; }) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card card-stats bg-info text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="card-title mb-0 text-uppercase">Rata-rata</small>
                            <h3 class="mb-0">
                                @php
                                    $avg = $siswas->avg(function($s) { return $s->detail_kehadiran['persentase'] ?? 0; });
                                @endphp
                                {{ number_format($avg, 1) }}%
                            </h3>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card card-stats bg-danger text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="card-title mb-0 text-uppercase">Total Alpa</small>
                            <h3 class="mb-0">{{ $siswas->sum(function($s) { return $s->detail_kehadiran['alpa'] ?? 0; }) }}</h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-2">
            <h5 class="mb-0 small fw-bold">
                <i class="fas fa-table"></i> Data Absensi Harian
            </h5>
        </div>
        <div class="card-body p-0">
            <div style="overflow-x: auto; width: 100%;">
                <table class="table table-bordered table-hover table-sm" id="absensi-table" style="min-width: 800px; width: 100%;">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 12%;">NIS</th>
                            <th style="width: 20%;">Nama Siswa</th>
                            <th style="width: 8%;">Kelas</th>
                            <th class="text-center" style="width: 8%;">Hadir</th>
                            <th class="text-center" style="width: 8%;">Sakit</th>
                            <th class="text-center" style="width: 8%;">Izin</th>
                            <th class="text-center" style="width: 8%;">Alpa</th>
                            <th class="text-center" style="width: 10%;">Persentase</th>
                            <th class="text-center" style="width: 10%;">Nilai</th>
                            <th class="text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $index => $item)
                        @php
                            $detail = $item->detail_kehadiran;
                            $persen = (int)($detail['persentase'] ?? 0);
                            $hadirCount = $detail['hadir'] ?? 0;
                            $sakitCount = $detail['sakit'] ?? 0;
                            $izinCount = $detail['izin'] ?? 0;
                            $alpaCount = $detail['alpa'] ?? 0;

                            if ($persen >= 90) {
                                $badgeClass = 'success';
                            } elseif ($persen >= 75) {
                                $badgeClass = 'warning';
                            } else {
                                $badgeClass = 'danger';
                            }
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="small">{{ $item->nis }}</td>
                            <td class="small">{{ $item->nama_siswa }}</td>
                            <td class="small">{{ $item->kelas }}</td>
                            <td class="text-center">{{ $hadirCount }}</td>
                            <td class="text-center">{{ $sakitCount }}</td>
                            <td class="text-center">{{ $izinCount }}</td>
                            <td class="text-center">{{ $alpaCount }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center">
                                    <div class="progress mr-1" style="height: 4px; width: 40px;">
                                        <div class="progress-bar bg-{{ $badgeClass }}"
                                            role="progressbar"
                                            style="width: <?= $persen ?>%;"></div>
                                    </div>
                                    <span class="small"><?= $persen ?>%</span>
                                </div>
                                <br>
                                <small class="text-muted">({{ $hadirCount }}/{{ $detail['total_absensi'] ?? 0 }} hari)</small>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $badgeClass }}">{{ $item->nilai_kehadiran }}</strong>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('absensi.rekap', $item->id) }}"
                                       class="btn btn-info btn-sm py-0 px-2"
                                       title="Rekap"
                                       style="background: #17a2b8; border-color: #17a2b8; padding: 4px 8px; font-size: 0.7rem;">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    @if(auth()->user() && (auth()->user()->isAdmin() || auth()->user()->isGuru()))
                                    <a href="{{ route('absensi.create', $item->id) }}"
                                       class="btn btn-primary btn-sm py-0 px-2"
                                       title="Tambah Absensi"
                                       style="background: #1e3c72; border-color: #1e3c72; padding: 4px 8px; font-size: 0.7rem;">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
    var table = $('#absensi-table');
    if (table.length) {
        // Hancurkan instance DataTable jika sudah ada
        if ($.fn.dataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

        // Inisialisasi DataTable
        table.DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
            },
            "paging": false,
            "ordering": true,
            "info": true,
            "searching": true,
            "order": [[2, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [0, 10] }
            ],
            "responsive": true,
            "autoWidth": false,
            "scrollX": true,
            "scrollCollapse": true
        });
    }
});
</script>
@endpush
