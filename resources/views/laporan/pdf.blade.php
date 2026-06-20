<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penilaian Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            padding: 10px;
            margin: 0;
        }
        
        /* KOP SEKOLAH - LOGO KIRI, TEKS TENGAH */
        .kop {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            position: relative;
        }
        
        .kop-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .logo-left {
            position: absolute;
            left: 0;
            width: 80px;
            text-align: center;
        }
        
        .logo-left img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .kop-text {
            flex: 1;
            text-align: center;
        }
        
        .kop-text h1 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
        }
        
        .kop-text h2 {
            font-size: 11px;
            font-weight: normal;
            margin: 3px 0;
        }
        
        .kop-text p {
            font-size: 9px;
            margin: 2px 0;
        }
        
        /* JUDUL */
        .judul {
            text-align: center;
            margin: 10px 0;
        }
        
        .judul h3 {
            font-size: 12px;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        
        .judul p {
            font-size: 9px;
            margin: 2px 0;
        }
        
        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
        }
        
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 10px;
        }
        
        td {
            font-size: 9px;
        }
        
        .text-left {
            text-align: left;
        }
        
        /* TANDA TANGAN */
        .ttd {
            margin-top: 40px;
            text-align: right;
        }
        
        .ttd p {
            margin: 4px 0;
            font-size: 10px;
        }
        
        .ttd .tempat-tanggal {
            margin-bottom: 15px;
        }
        
        .ttd .jabatan {
            margin-top: 10px;
        }
        
        .ttd .nama {
            margin-top: 35px;
            margin-bottom: 5px;
        }
        
        .ttd .nama p {
            font-size: 11px;
            font-weight: bold;
        }
        
        .ttd .nip {
            margin-top: 3px;
        }
        
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- KOP SEKOLAH - LOGO KIRI, TEKS TENGAH -->
    <div class="kop">
        <div class="kop-container">
            <div class="logo-left">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo MIN 3 Tangerang" onerror="this.style.display='none'">
            </div>
            <div class="kop-text">
                <h1>MADRASAH IBTIDAIYAH NEGERI (MIN) 3 TANGERANG</h1>
                <h2>Kementerian Agama Republik Indonesia</h2>
                <p>Jl. Kp. Sawah, Lengkong Kulon, Kec. Pagedangan, Kabupaten Tangerang, Banten 15331</p>
                <p>Email: min03tangerang@gmail.com</p>
            </div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul">
        <h3>LAPORAN PENILAIAN SISWA</h3>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <!-- TABEL -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Rangking</th>
                <th width="15%">NIS</th>
                <th width="30%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="25%">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $result)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $result['rangking'] }}</td>
                <td>{{ $result['alternatif']->nis }}</td>
                <td class="text-left">{{ $result['alternatif']->nama_siswa }}</td>
                <td>{{ $result['alternatif']->kelas }}</td>
                <td><strong>{{ number_format($result['total_nilai'], 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN KEPALA SEKOLAH -->
    <div class="ttd">
        <div class="tempat-tanggal">
            <p>Tangerang, {{ date('d F Y') }}</p>
        </div>
        <div class="jabatan">
            <p>Kepala Madrasah,</p>
        </div>
        <div class="nama">
            <p><strong>{{ $kepalaSekolah ?? 'H. MUNSARI, M.Pd' }}</strong></p>
        </div>
        <div class="nip">
            <p>NIP. 196904201999031002</p>
        </div>
    </div>

</body>
</html>