@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Sub Kriteria: {{ $kriteria->nama_kriteria }}</h2>
        </div>
        <div class="col-md-6 text-right">
            {{-- HANYA ADMIN YANG BISA MELIHAT TOMBOL TAMBAH --}}
            @if(auth()->user() && auth()->user()->isAdmin())
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalSub">
                <i class="fas fa-plus"></i> Tambah Sub Kriteria
            </button>
            @endif
            <a href="{{ route('kriteria.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Sub Kriteria</th>
                            <th>Nilai</th>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subKriterias as $index => $sub)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sub->nama_sub }}</td>
                            <td>{{ $sub->nilai }}</td>
                            @if(auth()->user() && auth()->user()->isAdmin())
                            <td>
                                <form action="{{ route('kriteria.sub.destroy', $sub->id) }}" method="POST" class="d-inline">
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

@if(auth()->user() && auth()->user()->isAdmin())
<!-- Modal Tambah Sub Kriteria - HANYA UNTUK ADMIN -->
<div class="modal fade" id="modalSub" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sub Kriteria</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('kriteria.sub.store', $kriteria->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Sub Kriteria</label>
                        <input type="text" name="nama_sub" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nilai</label>
                        <input type="number" name="nilai" class="form-control" min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection