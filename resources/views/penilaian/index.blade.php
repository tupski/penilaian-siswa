@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <h2>Data Penilaian Siswa</h2>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <div class="d-flex flex-wrap justify-content-md-end justify-content-start" style="gap: 5px;">
                <a href="{{ route('penilaian.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Input Penilaian
                </a>
                <a href="{{ route('penilaian.export-pdf') }}" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('penilaian.export-csv') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" id="penilaian-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alternatifs as $index => $siswa)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama_siswa }}</td>
                            <td>{{ $siswa->kelas }}</td>
                            <td>
                                <a href="{{ route('penilaian.edit', $siswa->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Input/Edit Nilai
                                </a>
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#penilaian-table');

        // Hancurkan instance DataTable jika sudah ada
        if ($.fn.dataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

        // Inisialisasi DataTable
        table.DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
            },
            "pageLength": 10,
            "responsive": true
        });
    });
</script>
@endpush
