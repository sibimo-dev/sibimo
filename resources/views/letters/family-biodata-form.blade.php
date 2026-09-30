{{--
    Formulir Isian Biodata Penduduk WNI (Per Keluarga) - F-1.01
    Lokasi: resources/views/letters/family-biodata-form.blade.php
    Standalone (tidak extends layouts.base) -> landscape folio, 1 halaman.

    Variabel dari LetterPdfService::viewData():
      $logo, $kodeForm, $region, $form

    Key $form yang dibaca:
      head_name, address, postal_code, rt, rw, member_count, phone,
      province, regency, district, village, hamlet, kk_number,
      village_head_name, hamlet_head_name   (semua opsional)
      members[] (maks 8 baris), tiap baris boleh berisi key:
        name, nik, previous_address, passport_number, passport_expiry,
        gender, birth_place, birth_date, age, birth_cert, birth_cert_number,
        blood_type, religion, marital_status, marriage_cert, marriage_cert_number,
        marriage_date, divorce_cert, divorce_cert_number, divorce_date,
        family_status, disability_mental, disability_type, education, occupation,
        mother_nik, mother_name, father_nik, father_name
      Kolom berkotak diisi KODE sesuai petunjuk F-1.01.

    CATATAN: lebar kolom sengaja ditaruh di <th> (bukan <colgroup>) karena
    DomPDF mengabaikan <col>.
--}}
@php
    $members = collect($form['members'] ?? [])->values();
    $totalRows = 8;

    $f = fn ($key) => $form[$key] ?? '';
    $m = fn ($i, $key) => $members->get($i)[$key] ?? '';

    // Bagian "Diisi Oleh Petugas": DIKOSONGKAN (diisi petugas tulisan tangan).
    // Hanya terisi kalau $form membawa province_code/province, regency_code/regency, dst.
    // Untuk mengisi otomatis dengan default service ($region), ganti dengan:
    //   $rg = $region ?? [];
    //   $code = fn ($k) => $f($k . '_code') ?: implode('', $rg[$k . '_code'] ?? []);
    //   $name = fn ($k) => $f($k) ?: ($rg[$k . '_name'] ?? '');
    $code = fn ($k) => $f($k . '_code');
    $name = fn ($k) => $f($k);

    $fmtDate = function ($v) {
        if (blank($v)) return '';
        try { return \Illuminate\Support\Carbon::parse($v)->format('d/m/Y'); }
        catch (\Throwable $e) { return $v; }
    };

    $ageOf = function ($i) use ($members) {
        $r = $members->get($i) ?? [];
        if (! blank($r['age'] ?? null)) return $r['age'];
        if (blank($r['birth_date'] ?? null)) return '';
        try { return \Illuminate\Support\Carbon::parse($r['birth_date'])->age; }
        catch (\Throwable $e) { return ''; }
    };

    $memberCount = $f('member_count') !== '' ? $f('member_count') : ($members->count() ?: '');

    // Kotak tunggal di dalam sel (kolom berkode: jenis kelamin, agama, dst.).
    $box = fn ($v) => new \Illuminate\Support\HtmlString('<div class="box">' . e($v) . '</div>');

    // Kotak digit tunggal (satu digit per kotak) untuk kode wilayah.
    $cells = function ($value, $n) {
        $chars = str_split(preg_replace('/\s+/', '', (string) $value));
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= '<td class="dg' . ($i === $n - 1 ? ' last' : '') . '">' . e($chars[$i] ?? '') . '</td>';
        }
        return new \Illuminate\Support\HtmlString($out);
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Formulir Biodata Penduduk WNI (Per Keluarga)</title>
    <style>
        /* Folio landscape: 33 x 21.5 cm, margin tipis supaya muat 1 halaman */
        @page { size: 33cm 21.5cm; margin: 0.5cm 0.9cm 0.4cm 0.9cm; }

        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 6.5pt; line-height: 1.1; color: #000; margin: 0; }

        table { border-collapse: collapse; }
        td, th { vertical-align: middle; }

        /* ---------- Kop ---------- */
        table.kop { width: 100%; }
        table.kop td { padding: 0; }
        .kop-title { text-align: center; font-weight: bold; font-size: 11pt; line-height: 1.15; }
        .kop-sub { text-align: center; font-size: 7pt; line-height: 1.2; }
        .kode-form {
            border: 1px solid #000; width: 56pt; text-align: center;
            font-weight: bold; font-size: 8pt; padding: 2pt 0; float: right;
        }
        .form-bar {
            text-align: center; font-weight: bold; font-size: 8.5pt;
            border-top: 1px solid #000; border-bottom: 1px solid #000;
            margin-top: 2pt; padding: 0.5pt 0;
        }

        /* ---------- Data kepala keluarga + diisi petugas ---------- */
        table.top { width: 100%; margin-top: 1.5pt; table-layout: fixed; }
        table.top > tbody > tr > td { padding: 0; vertical-align: top; }

        table.lt, table.rt { width: 100%; table-layout: fixed; border-collapse: separate; border-spacing: 0 1.5pt; }
        table.lt td, table.rt td { padding: 0 2pt; font-size: 6.5pt; vertical-align: middle; }
        tr.sp td { height: 0; padding: 0; font-size: 0; line-height: 0; border: 0; }

        .sec-title { font-weight: bold; font-size: 6.5pt; margin-top: 0.5pt; }
        td.sec { font-weight: bold; padding: 0; }
        td.per { background: #cfcfcf; border: 1px solid #000; font-size: 6pt; padding: 1pt 3pt; height: 0.36cm; }
        td.k { font-weight: bold; padding-left: 0; }
        td.bx { border: 1px solid #000; height: 0.38cm; padding: 0 3pt; }
        td.ctr { text-align: center; }
        td.lbl { text-align: right; font-weight: bold; padding: 0 4pt; }
        td.lbl-l { text-align: left; font-weight: bold; padding: 0 3pt; }

        /* kotak digit tunggal: kiri/atas/bawah per kotak, kanan hanya di kotak terakhir */
        td.dg { border: 1px solid #000; border-right: 0; height: 0.38cm; text-align: center; padding: 0; }
        td.dg.last { border-right: 1px solid #000; }
        td.nm { border: 1px solid #000; height: 0.38cm; padding: 0 3pt; }

        /* ---------- Tabel isian ---------- */
        /* garis tepi tebal = border wrapper (.gw) + border sel terluar */
        .gw { border: 1px solid #000; margin-top: 2pt; }
        table.grid { width: 100%; table-layout: fixed; border-collapse: collapse; }
        table.grid th, table.grid td { border: 1px solid #000; padding: 0.5pt 2pt; font-size: 5.8pt; line-height: 1.1; word-wrap: break-word; }
        table.grid th { font-weight: bold; text-align: center; padding: 1pt 1.5pt; }
        table.grid tr.num td { background: #b9b9b9; text-align: center; font-weight: bold; padding: 0; font-size: 5.5pt; line-height: 1; }

        /* badan tabel: hanya garis vertikal antar kolom, tanpa garis antar baris */
        table.grid tbody tr { page-break-inside: avoid; }
        table.grid tbody td { height: 0.34cm; border-top: 0; border-bottom: 0; }
        table.grid tbody tr.last td { border-bottom: 1px solid #000; }
        table.grid td.c { text-align: center; }
        table.grid td.no { text-align: left; font-weight: bold; padding-left: 4pt; }

        /* kolom berkotak: latar abu-abu + kotak putih tunggal per baris */
        table.grid td.bxc { background: #e4e4e4; padding: 0; text-align: center; }
        .box { width: 17pt; height: 5.6pt; margin: 1.3pt auto; border: 1px solid #000; background: #fff;
               font-size: 5.3pt; line-height: 5.6pt; text-align: center; overflow: hidden; }

        /* ---------- Pernyataan & TTD ---------- */
        table.foot { width: 100%; margin-top: 3pt; table-layout: fixed; page-break-inside: avoid; }
        table.foot td { vertical-align: top; font-size: 6pt; padding: 0 4pt; }
        table.foot td.pernyataan { border: 1px solid #000; padding: 2pt 4pt; }
        .pernyataan-title { font-weight: bold; text-decoration: underline; }
        .ttd-space { height: 0.9cm; }
        .ttd-title { font-weight: bold; }
    </style>
</head>
<body>

    {{-- ===== KOP ===== --}}
    <table class="kop">
        <tr>
            <td style="width:32%; text-align:right; padding-right:0.3cm; vertical-align:middle;">
                @if(!empty($logo))<img src="{{ $logo }}" style="height:1.4cm;">@endif
            </td>
            <td style="width:36%; white-space:nowrap;">
                <div class="kop-title">DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</div>
                <div class="kop-sub">
                    Jalan : KRT Pringgodiningrat No 3 Beran - Tridadi - Sleman Daerah Istimewa Yogyakarta<br>
                    Telp. (0274) 868 362 Kode Pos 55511
                </div>
            </td>
            <td style="width:32%; vertical-align: top;">
                <div class="kode-form">{{ $kodeForm ?? 'F-1.01' }}</div>
            </td>
        </tr>
    </table>

    <div class="form-bar">FORMULIR ISIAN BIODATA PENDUDUK WNI (PER KELUARGA )</div>

    {{-- ===== DATA KEPALA KELUARGA (kiri) + DIISI PETUGAS (kanan) ===== --}}
    <table class="top">
        <tr>
            {{-- ---------- KIRI ---------- --}}
            <td style="width:62%; padding-right:10pt;">
                <table class="lt">
                    {{-- baris pengunci lebar 9 kolom --}}
                    <tr class="sp">
                        <td style="width:21%"></td>  {{-- 1 label --}}
                        <td style="width:15%"></td>  {{-- 2 kotak Kode Pos --}}
                        <td style="width:14%"></td>  {{-- 3 label RT --}}
                        <td style="width:8%"></td>   {{-- 4 kotak RT --}}
                        <td style="width:6.5%"></td> {{-- 5 label RW --}}
                        <td style="width:13%"></td>  {{-- 6 kotak RW --}}
                        <td style="width:12.5%"></td>{{-- 7 label Jumlah Anggota --}}
                        <td style="width:6.5%"></td> {{-- 8 kotak Jumlah --}}
                        <td style="width:3.5%"></td> {{-- 9 "orang" --}}
                    </tr>
                    <tr>
                        <td class="per" colspan="4">PERHATIAN : Isilah Formulir ini dengan huruf cetak dan jelas</td>
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td class="sec" colspan="9">DATA KEPALA KELUARGA</td>
                    </tr>
                    <tr>
                        <td class="k">Nama Kepala Keluarga</td>
                        <td class="bx" colspan="7">{{ $f('head_name') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="k">Alamat</td>
                        <td class="bx" colspan="7">{{ $f('address') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="k">Kode Pos</td>
                        <td class="bx ctr">{{ $f('postal_code') }}</td>
                        <td class="lbl">RT</td>
                        <td class="bx ctr">{{ $f('rt') }}</td>
                        <td class="lbl">RW</td>
                        <td class="bx ctr">{{ $f('rw') }}</td>
                        <td class="lbl">Jumlah Anggota Keluarga</td>
                        <td class="bx ctr">{{ $memberCount }}</td>
                        <td class="lbl-l">orang</td>
                    </tr>
                    <tr>
                        <td class="k">Telepon</td>
                        <td class="bx" colspan="3">{{ $f('phone') }}</td>
                        <td colspan="5"></td>
                    </tr>
                </table>
            </td>

            {{-- ---------- KANAN ---------- --}}
            <td style="width:38%;">
                <table class="rt">
                    {{-- baris pengunci lebar 7 kolom --}}
                    <tr class="sp">
                        <td style="width:32%"></td>
                        <td style="width:4.5%"></td>
                        <td style="width:4.5%"></td>
                        <td style="width:4.5%"></td>
                        <td style="width:4.5%"></td>
                        <td style="width:2%"></td>
                        <td style="width:48%"></td>
                    </tr>
                    <tr>
                        <td class="k" colspan="7">Diisi Oleh Petugas</td>
                    </tr>
                    <tr>
                        <td class="k">Kode- Nama Provinsi</td>
                        {{ $cells($code('province'), 2) }}
                        <td colspan="2"></td>
                        <td></td>
                        <td class="nm">{{ $name('province') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Kode-Nama Kab</td>
                        {{ $cells($code('regency'), 2) }}
                        <td colspan="2"></td>
                        <td></td>
                        <td class="nm">{{ $name('regency') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Kode-Kecamatan</td>
                        {{ $cells($code('district'), 2) }}
                        <td colspan="2"></td>
                        <td></td>
                        <td class="nm">{{ $name('district') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Kode-Nama Desa</td>
                        {{ $cells($code('village'), 4) }}
                        <td></td>
                        <td class="nm">{{ $name('village') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Nama Dusun</td>
                        {{ $cells($f('hamlet_code'), 2) }}
                        <td colspan="2"></td>
                        <td></td>
                        <td class="nm">{{ $f('hamlet') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Nomor KK</td>
                        <td colspan="6" style="font-size:7pt; letter-spacing:0.5pt;">{{ $f('kk_number') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="sec-title" style="margin-top:2pt;">DATA KELUARGA</div>

    {{-- ===== TABEL 1 (kolom 1-6) ===== --}}
    <div class="gw" style="margin-top:1pt;">
    <table class="grid">
        <thead>
            <tr>
                <th style="width:2%">No</th>
                <th style="width:49.5%">Nama Lengkap</th>
                <th style="width:9.5%">Nomor KTP/Nopen</th>
                <th style="width:23%">Alamat Sebelumnya</th>
                <th style="width:6%">Nomor Paspor</th>
                <th style="width:10%">Tanggal Berakhir Paspor</th>
            </tr>
            <tr class="num"><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td></tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $totalRows; $i++)
                <tr @if ($i === $totalRows - 1) class="last" @endif>
                    <td class="no">{{ $i + 1 }}</td>
                    <td>{{ $m($i, 'name') }}</td>
                    <td>{{ $m($i, 'nik') }}</td>
                    <td>{{ $m($i, 'previous_address') }}</td>
                    <td>{{ $m($i, 'passport_number') }}</td>
                    <td class="c">{{ $fmtDate($m($i, 'passport_expiry')) }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
    </div>

    {{-- ===== TABEL 2 (kolom 7-21) ===== --}}
    <div class="gw">
    <table class="grid">
        <thead>
            <tr>
                <th style="width:1.8%; vertical-align:bottom;">No</th>
                <th style="width:5%; vertical-align:bottom;">Jenis Kelamin</th>
                <th style="width:12.4%">Tempat Lahir</th>
                <th style="width:7.4%">Tanggal/Bulan/ Tahun Lahir</th>
                <th style="width:4%">Umur</th>
                <th style="width:4.2%">Akte Lahir/ Surat Lahir</th>
                <th style="width:12.6%">Nomor Akta Kelahiran/ Surat Kenal Lahir</th>
                <th style="width:4%">Golongan Darah</th>
                <th style="width:3.2%">AGAMA</th>
                <th style="width:7%">Status Perkawinan</th>
                <th style="width:5.2%">Akta Perkawinan/ Buku Nikah</th>
                <th style="width:7.4%">Nomor Akta Perkawinan/Buku Nikah*)</th>
                <th style="width:5.9%">Tanggal Perkawinan *)</th>
                <th style="width:4.4%">Akte Cerai/ Surat Cerai*)</th>
                <th style="width:6.3%">Nomor Akta Perceraian/Surat Cerai*)</th>
                <th style="width:9.2%">Tanggal Perceraian*)</th>
            </tr>
            <tr class="num">
                <td>&nbsp;</td>
                @foreach (range(7, 21) as $n)<td>{{ $n }}</td>@endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $totalRows; $i++)
                <tr @if ($i === $totalRows - 1) class="last" @endif>
                    <td class="no">{{ $i + 1 }}</td>
                    <td class="bxc">{{ $box($m($i, 'gender')) }}</td>
                    <td>{{ $m($i, 'birth_place') }}</td>
                    <td class="c">{{ $fmtDate($m($i, 'birth_date')) }}</td>
                    <td class="c">{{ $ageOf($i) }}</td>
                    <td class="bxc">{{ $box($m($i, 'birth_cert')) }}</td>
                    <td>{{ $m($i, 'birth_cert_number') }}</td>
                    <td class="bxc">{{ $box($m($i, 'blood_type')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'religion')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'marital_status')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'marriage_cert')) }}</td>
                    <td>{{ $m($i, 'marriage_cert_number') }}</td>
                    <td class="c">{{ $fmtDate($m($i, 'marriage_date')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'divorce_cert')) }}</td>
                    <td>{{ $m($i, 'divorce_cert_number') }}</td>
                    <td class="c">{{ $fmtDate($m($i, 'divorce_date')) }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
    </div>

    {{-- ===== TABEL 3 (kolom 22-30) ===== --}}
    <div class="gw">
    <table class="grid">
        <thead>
            <tr>
                <th style="width:2.5%; vertical-align:bottom;">No</th>
                <th style="width:5%">Status Hub. Dlm Keluarga</th>
                <th style="width:5%">Kelainan Fisik &amp; Mental</th>
                <th style="width:5%">Penyandang Cacat</th>
                <th style="width:5%">Pendidikan Terakhir</th>
                <th style="width:5%">Pekerjaan</th>
                <th style="width:18%">NIK IBU</th>
                <th style="width:19.5%">Nama lengkap Ibu</th>
                <th style="width:18%">NIK Ayah</th>
                <th style="width:17%">Nama Lengkap Ayah</th>
            </tr>
            <tr class="num">
                <td>&nbsp;</td>
                @foreach (range(22, 30) as $n)<td>{{ $n }}</td>@endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $totalRows; $i++)
                <tr @if ($i === $totalRows - 1) class="last" @endif>
                    <td class="no">{{ $i + 1 }}</td>
                    <td class="bxc">{{ $box($m($i, 'family_status')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'disability_mental')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'disability_type')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'education')) }}</td>
                    <td class="bxc">{{ $box($m($i, 'occupation')) }}</td>
                    <td>{{ $m($i, 'mother_nik') }}</td>
                    <td>{{ $m($i, 'mother_name') }}</td>
                    <td>{{ $m($i, 'father_nik') }}</td>
                    <td>{{ $m($i, 'father_name') }}</td>
                </tr>
            @endfor
        </tbody>
    </table>
    </div>

    {{-- ===== PERNYATAAN & TANDA TANGAN ===== --}}
    <table class="foot">
        <tr>
            <td class="pernyataan" style="width:22%;">
                <div class="pernyataan-title">PERNYATAAN</div>
                Demikian Formulir ini saya/kami isi dengan sesungguhnya apabila keterangan tersebut tidak sesuai
                dengan keadaan Sebenarnya, saya bersedia dikenakan sanksi sesuai ketentuan.
            </td>
            <td style="width:19%;">
                <div class="ttd-title">Mengetahui,<br>Petugas kecamatan</div>
                <div class="ttd-space"></div>
                NIP :
            </td>
            <td style="width:19%;">
                <div class="ttd-title">Mengetahui,<br>Kepala Desa</div>
                <div class="ttd-space"></div>
                {{ $f('village_head_name') }}
            </td>
            <td style="width:19%;">
                <div class="ttd-title">Mengetahui,<br>Dukuh</div>
                <div class="ttd-space"></div>
                {{ $f('hamlet_head_name') }}
            </td>
            <td style="width:21%;">
                <div class="ttd-title">Kepala Keluarga,</div>
                <div class="ttd-space"></div>
                {{ $f('head_name') }}
            </td>
        </tr>
    </table>

</body>
</html>