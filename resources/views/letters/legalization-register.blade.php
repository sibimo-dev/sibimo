{{--
    Register Legalisasi
    Lokasi: resources/views/letters/legalization-register.blade.php
    Standalone (tidak extends layouts.base) -> tanpa kop, landscape folio.

    Data baris dibaca dari $form['rows'] (array). Tiap baris boleh berisi key:
      date, kk_number, address, rt, rw, nik, name, gender, birth_place,
      birth_date, age (opsional, dihitung dari birth_date kalau kosong),
      religion, marital_status, family_status, education, occupation
    Kalau kurang dari 15 baris, sisanya diisi baris kosong bernomor.
--}}
@php
    $rows = collect($form['rows'] ?? [])->values();
    $minRows = 15;
    $total = max($minRows, $rows->count());

    $fmtDate = function ($v) {
        if (blank($v)) return '';
        try { return \Illuminate\Support\Carbon::parse($v)->format('d/m/Y'); }
        catch (\Throwable $e) { return $v; }
    };

    $ageOf = function ($row) {
        if (! blank($row['age'] ?? null)) return $row['age'];
        if (blank($row['birth_date'] ?? null)) return '';
        try { return \Illuminate\Support\Carbon::parse($row['birth_date'])->age; }
        catch (\Throwable $e) { return ''; }
    };

    $registerYear = $form['register_year'] ?? $year ?? now()->format('Y');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Register Legalisasi {{ $registerYear }}</title>
    <style>
        /* Folio landscape: 33 x 21.5 cm */
        @page { size: 33cm 21.5cm; margin: 1cm 1cm 1cm 1cm; }

        * { box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7pt;
            color: #000;
            margin: 0;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        .title p { margin: 0 0 8px 0; }

        table.register {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.register th,
        table.register td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        table.register thead { display: table-header-group; }
        table.register tr { page-break-inside: avoid; }

        table.register th {
            background: #6aa329;
            color: #fff;
            font-weight: bold;
            font-size: 6.5pt;
            text-align: center;
            height: 0.9cm;
            text-transform: uppercase;
        }
        table.register th.umur { background: #6b6b6b; }

        table.register td {
            height: 0.85cm;
            font-size: 6.5pt;
        }
        table.register td.c { text-align: center; }
        table.register td.no { text-align: center; font-weight: bold; }
    </style>
</head>
<body>

    <div class="title">
        <p>REGISTER LEGALISASI</p>
        <p>TAHUN {{ $registerYear }}</p>
        <p>KALURAHAN BIMOMARTANI, KAPANEWON NGEMPLAK</p>
    </div>

    <table class="register">
        <colgroup>
            <col style="width:2.5%">  {{-- No --}}
            <col style="width:5%">    {{-- Tanggal --}}
            <col style="width:8%">    {{-- Nomor KK --}}
            <col style="width:9%">    {{-- Alamat --}}
            <col style="width:2.5%">  {{-- RT --}}
            <col style="width:2.5%">  {{-- RW --}}
            <col style="width:8%">    {{-- NIK --}}
            <col style="width:10%">   {{-- Nama Lengkap --}}
            <col style="width:2.5%">  {{-- L/P --}}
            <col style="width:6.5%">  {{-- Tempat Lahir --}}
            <col style="width:5%">    {{-- Tanggal Lahir --}}
            <col style="width:3%">    {{-- Umur --}}
            <col style="width:4.5%">  {{-- Agama --}}
            <col style="width:4.5%">  {{-- Status Kawin --}}
            <col style="width:6.5%">  {{-- Status Hub dlm Keluarga --}}
            <col style="width:10%">   {{-- Pendidikan Terakhir --}}
            <col style="width:9.5%">  {{-- Jenis Pekerjaan --}}
        </colgroup>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nomor KK</th>
                <th>Alamat</th>
                <th>RT</th>
                <th>RW</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th class="umur">Umur</th>
                <th>Agama</th>
                <th>Status Kawin</th>
                <th>Status Hub Dlm Keluarga</th>
                <th>Pendidikan Terakhir</th>
                <th>Jenis Pekerjaan</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $total; $i++)
                @php $r = $rows->get($i, []); @endphp
                <tr>
                    <td class="no">{{ $i + 1 }}</td>
                    <td class="c">{{ $fmtDate($r['date'] ?? null) }}</td>
                    <td>{{ $r['kk_number'] ?? '' }}</td>
                    <td>{{ $r['address'] ?? '' }}</td>
                    <td class="c">{{ $r['rt'] ?? '' }}</td>
                    <td class="c">{{ $r['rw'] ?? '' }}</td>
                    <td>{{ $r['nik'] ?? '' }}</td>
                    <td>{{ $r['name'] ?? '' }}</td>
                    <td class="c">{{ $r['gender'] ?? '' }}</td>
                    <td>{{ $r['birth_place'] ?? '' }}</td>
                    <td class="c">{{ $fmtDate($r['birth_date'] ?? null) }}</td>
                    <td class="c">{{ $ageOf($r) }}</td>
                    <td class="c">{{ $r['religion'] ?? '' }}</td>
                    <td class="c">{{ $r['marital_status'] ?? '' }}</td>
                    <td>{{ $r['family_status'] ?? '' }}</td>
                    <td>{{ $r['education'] ?? '' }}</td>
                    <td>{{ $r['occupation'] ?? '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

</body>
</html>