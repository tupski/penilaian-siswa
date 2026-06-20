@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Hasil Rangking Siswa Terbaik</h2>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Rangking</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Nilai Akhir</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr @if($result['rangking'] == 1) class="table-success" @endif>
                            <td>
                                @if($result['rangking'] == 1)
                                    <i class="fas fa-trophy text-warning"></i> 
                                @endif
                                {{ $result['rangking'] }}
                            </td>
                            <td>{{ $result['alternatif']->nis }}</td>
                            <td>{{ $result['alternatif']->nama_siswa }}</td>
                            <td>{{ $result['alternatif']->kelas }}</td>
                            <td><strong>{{ number_format($result['total_nilai'], 2) }}</strong></td>
                            <td>
                                @if($result['rangking'] == 1)
                                    <span class="badge badge-success">Siswa Terbaik</span>
                                @elseif($result['rangking'] <= 3)
                                    <span class="badge badge-info">Peringkat {{ $result['rangking'] }}</span>
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
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