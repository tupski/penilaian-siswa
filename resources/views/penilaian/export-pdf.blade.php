<!DOCTYPE html>
<html>
<head>
    <title>Data Penilaian Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            padding: 20px;
            margin: 0;
        }
        
        /* KOP SEKOLAH - LOGO KIRI, TEKS TENGAH */
        .kop {
            margin-bottom: 15px;
            padding-bottom: 10px;
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
            letter-spacing: 1px;
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
            margin: 15px 0;
        }
        
        .judul h3 {
            font-size: 13px;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .judul p {
            font-size: 9px;
            margin: 2px 0;
        }
        
        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        
        th {
            background-color: #e0e0e0;
            font-size: 10px;
            font-weight: bold;
        }
        
        td {
            font-size: 9px;
        }
        
        .text-left {
            text-align: left;
        }
        
        /* TANDA TANGAN */
        .ttd {
            margin-top: 35px;
            text-align: right;
        }
        
        .ttd p {
            margin: 3px 0;
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
        
        /* FOOTER */
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
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

    <!-- JUDUL LAPORAN -->
    <div class="judul">
        <h3>DATA PENILAIAN SISWA</h3>
        <p>Tanggal Cetak: {{ $tanggalCetak }}</p>
    </div>

    <!-- TABEL DATA PENILAIAN -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">NIS</th>
                <th width="30%">Nama Siswa</th>
                <th width="10%">Kelas</th>
                @foreach($kriterias as $kriteria)
                <th width="10%">{{ $kriteria->nama_kriteria }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['nis'] }}</td>
                <td class="text-left">{{ $item['nama'] }}</td>
                <td>{{ $item['kelas'] }}</td>
                @foreach($kriterias as $kriteria)
                <td>{{ $item[$kriteria->nama_kriteria] ?? 0 }}</td>
                @endforeach
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