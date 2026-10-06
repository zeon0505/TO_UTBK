<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-box {
            width: 65px;
            height: 65px;
            background: #2563eb;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            line-height: 65px;
            border-radius: 8px;
        }
        .univ-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .univ-sub {
            font-size: 13px;
            font-weight: bold;
            color: #2563eb;
        }
        .univ-address {
            font-size: 9px;
            color: #64748b;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-table td {
            padding: 6px 10px;
            font-size: 10px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 130px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #0f172a;
        }
        .data-table td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #a16207; }
        
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10px;
        }
        .sign-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Header -->
    <table class="header-table">
        <tr>
            <td style="width: 75px;">
                <div class="logo-box">K</div>
            </td>
            <td>
                <div class="univ-title">UNIVERSITAS TEKNOLOGI AKADEMIK</div>
                <div class="univ-sub">FAKULTAS ILMU KOMPUTER & TEKNOLOGI INFORMASI</div>
                <div class="univ-address">Jl. Kampus Utama No. 10, Kota Akademik • Telp: (021) 555-0199 • Web: www.kampus.ac.id</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">LAPORAN REKAPITULASI NILAI UJIAN TENGAH SEMESTER (UTS)</div>

    <!-- Metadata Informasi Ujian -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Mata Kuliah</td>
            <td>: {{ $exam ? $exam->course->name . ' (' . $exam->course->code . ')' : 'Seluruh Mata Kuliah' }}</td>
            <td class="meta-label">Tahun Akademik</td>
            <td>: {{ $tahunAkademik }}</td>
        </tr>
        <tr>
            <td class="meta-label">Program Studi</td>
            <td>: {{ $prodi }}</td>
            <td class="meta-label">Dosen Pengampu</td>
            <td>: {{ $exam ? ($exam->lecturer->name ?? '-') : '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Sesi Ujian</td>
            <td>: {{ $exam ? $exam->title : 'Gabungan Sesi' }}</td>
            <td class="meta-label">Tanggal Cetak</td>
            <td>: {{ $printedAt }} (Oleh: {{ $printedBy }})</td>
        </tr>
    </table>

    <!-- Tabel Rekap Nilai -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">NIM</th>
                <th>Nama Mahasiswa</th>
                <th style="width: 90px;">Prodi</th>
                <th style="width: 50px;">Kelas</th>
                <th style="width: 140px;">Ujian UTS</th>
                <th style="width: 50px;">Nilai PG</th>
                <th style="width: 55px;">Nilai Essay</th>
                <th style="width: 60px;">Total Nilai</th>
                <th style="width: 45px;">Grade</th>
                <th style="width: 75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                @php
                    $total = $res->score;
                    $grade = 'E';
                    if ($total >= 85) $grade = 'A';
                    elseif ($total >= 75) $grade = 'B+';
                    elseif ($total >= 65) $grade = 'B';
                    elseif ($total >= 55) $grade = 'C';
                    elseif ($total >= 45) $grade = 'D';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $res->user->nim ?? '-' }}</td>
                    <td class="font-bold">{{ $res->user->name }}</td>
                    <td class="text-center">{{ $res->user->prodi ?? '-' }}</td>
                    <td class="text-center">{{ $res->user->kelas ?? '-' }}</td>
                    <td>{{ $res->exam->title }}</td>
                    <td class="text-center">{{ number_format($res->mc_score, 1) }}</td>
                    <td class="text-center">{{ number_format($res->essay_score, 1) }}</td>
                    <td class="text-center font-bold" style="font-size: 11px; color: #1e293b;">
                        {{ number_format($res->score, 1) }}
                    </td>
                    <td class="text-center font-bold">{{ $grade }}</td>
                    <td class="text-center">
                        @if($res->is_graded)
                            <span class="badge badge-success">Selesai</span>
                        @else
                            <span class="badge badge-warning">Koreksi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #64748b;">
                        Belum ada data nilai hasil ujian mahasiswa untuk kriteria ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Ketua Program Studi {{ $prodi }}</strong>
                <div class="sign-space"></div>
                <u>(__________________________)</u><br>
                NIP. 19750101 200501 1 001
            </td>
            <td>
                Kota Akademik, {{ now()->translatedFormat('d F Y') }}<br>
                <strong>Dosen Pengampu / Penanggung Jawab</strong>
                <div class="sign-space"></div>
                <u>( {{ $exam ? ($exam->lecturer->name ?? $printedBy) : $printedBy }} )</u><br>
                NIP. {{ $exam && $exam->lecturer ? ($exam->lecturer->nip ?? '19850101 201012 1 001') : '19850101 201012 1 001' }}
            </td>
        </tr>
    </table>

</body>
</html>
