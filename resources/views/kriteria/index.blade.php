@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <h2>Data Kriteria</h2>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            {{-- HANYA ADMIN YANG BISA MELIHAT TOMBOL TAMBAH --}}
            @if(auth()->user() && auth()->user()->isAdmin())
            <a href="{{ route('kriteria.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kriteria
            </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Bobot (%)</th>
                            <th>Jenis</th>
                            <th>Sub Kriteria</th>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kriterias as $index => $kriteria)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $kriteria->kode_kriteria }}</td>
                            <td>{{ $kriteria->nama_kriteria }}</td>
                            <td>{{ $kriteria->bobot }}</td>
                            <td>
                                <span class="badge badge-{{ $kriteria->jenis == 'benefit' ? 'success' : 'danger' }}">
                                    {{ $kriteria->jenis == 'benefit' ? 'Benefit' : 'Cost' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('kriteria.sub', $kriteria->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-list"></i> Sub Kriteria
                                </a>
                            </td>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <td>
                                <a href="{{ route('kriteria.edit', $kriteria->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('kriteria.destroy', $kriteria->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
