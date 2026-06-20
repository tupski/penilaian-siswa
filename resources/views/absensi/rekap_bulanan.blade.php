@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2><i class="fas fa-chart-bar"></i> Rekap Bulanan Kehadiran</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <select name="bulan" class="form-control">
                        @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tahun" class="form-control">
                        @for($i=2023; $i<=date('Y'); $i++)
                        <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Alpa</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapBulanan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['hadir'] }}</td>
                            <td>{{ $item['sakit'] }}</td>
                            <td>{{ $item['izin'] }}</td>
                            <td>{{ $item['alpa'] }}</td>
                            <td class="text-{{ $item['persen'] >= 90 ? 'success' : ($item['persen'] >= 75 ? 'warning' : 'danger') }}">
                                {{ $item['persen'] }}%
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