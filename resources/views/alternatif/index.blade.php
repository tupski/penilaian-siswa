@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <h2>Data Siswa</h2>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <a href="{{ route('alternatif.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Siswa
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable" id="siswa-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alternatifs as $index => $alternatif)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $alternatif->nis }}</td>
                        <td>{{ $alternatif->nama_siswa }}</td>
                        <td>{{ $alternatif->kelas }}</td>
                        <td>{{ $alternatif->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td>
                            <a href="{{ route('alternatif.edit', $alternatif->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('alternatif.destroy', $alternatif->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
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
        var table = $('#siswa-table');

        // Hancurkan instance DataTable jika sudah ada
        if ($.fn.dataTable.isDataTable(table)) {
            table.DataTable().destroy();
        }

        // Inisialisasi DataTable dengan responsive
        table.DataTable({
            "paging": false,
            "ordering": true,
            "info": true,
            "searching": true,
            "responsive": true,
            "scrollX": true,
            "scrollCollapse": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 5] }
            ]
        });
    });
</script>
@endpush
