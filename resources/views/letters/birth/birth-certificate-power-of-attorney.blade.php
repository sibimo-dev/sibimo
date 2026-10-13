{{-- Surat Kuasa pengurusan Akta Kelahiran. Standalone (tanpa kop).
     Font tetap Helvetica/Arial (sesuai contoh surat kuasa). Data: $birth, $village, $signature. --}}
@php
    $grantor = $birth['father'];   // pemberi kuasa
    $grantee = $birth['reporter']; // penerima kuasa
    $childName = $birth['child']['name'];
    $date = $grantee['report_date'] ?: '..........................';
    $party = fn (array $p) => [
        'Nama' => $p['name'] ?? null,
        'Pekerjaan' => $p['occupation'] ?? null,
        'Alamat' => $p['address'] ?? null,
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Kuasa</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 12pt; line-height: 1.3; color: #000; }
        p { margin: 0; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; text-decoration: underline; }
        table { border-collapse: collapse; }
        table.party { width: 100%; }
        table.party td { padding: 2pt 0; vertical-align: top; }
        table.party td.lbl { width: 90pt; padding-left: 60pt; }
        table.party td.sep { width: 12pt; }
        table.party td.val { border-bottom: 1px dashed #000; }
        .khusus { text-align: center; font-weight: bold; text-decoration: underline; margin-top: 8pt; }
        .para { margin-top: 8pt; text-align: justify; }
        .child { margin-top: 14pt; text-align: center; font-weight: bold; font-size: 11pt;
                 border-bottom: 1px solid #000; padding-bottom: 2pt; }
        table.cols { width: 100%; page-break-inside: avoid; }
        table.cols td { width: 50%; text-align: center; vertical-align: top; }
        .space { height: 64pt; }
        .line { display: inline-block; min-width: 130pt; border-bottom: 1px dashed #000; }
        table.know-wrap { width: 100%; page-break-inside: avoid; }
        table.know-wrap td { padding: 0; vertical-align: top; }
        td.know-offset { width: 33%; }
        table.know td { padding: 0; text-align: left; }
        table.know td.know-head { text-align: center; }
        table.know td.know-space { height: 44pt; }
    </style>
</head>
<body>
    <div class="title">SURAT KUASA</div>

    <p style="margin-top: 30pt;">Yang bertanda tangan dibawah ini</p>
    <table class="party">
        @foreach ($party($grantor) as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td><td class="sep">:</td>
                <td class="val">{{ $value }}</td>
            </tr>
            @if ($label === 'Alamat')
                <tr><td></td><td></td><td class="val">{{ $village }}</td></tr>
            @endif
        @endforeach
    </table>

    <p style="margin-top: 8pt;">Dengan ini memberi kuasa kepada</p>
    <table class="party">
        @foreach ($party($grantee) as $label => $value)
            <tr>
                <td class="lbl">{{ $label }}</td><td class="sep">:</td>
                <td class="val">{{ $value }}</td>
            </tr>
            @if ($label === 'Alamat')
                <tr><td></td><td></td><td class="val">{{ $village }}</td></tr>
            @endif
        @endforeach
    </table>

    <div class="khusus">KHUSUS</div>

    <p class="para">
        Untuk mewakili diri saya mengajukan permohonan Akta Kelahiran ke Dinas Kependudukan dan
        Pencatatan Sipil Kabupaten Sleman untuk anak:
    </p>

    <div class="child">{{ $childName }}</div>

    <p class="para">berhubung saya tidak dapat menghadap sendiri.</p>
    <p class="para">
        Pemegang kuasa ini diberi hak untuk menghadap dan berbicara dimuka Dinas Kependudukan dan
        Pencatatan Sipil Kabupaten Sleman, mengajukan saksi-saksi, mohon keputusan, menghadap dan
        berbicara dengan instansi-instansi lain dalam perkara ini
    </p>
    <p class="para">
        Pendek kata untuk melakukan segala sesuatu yang kesemuanya untuk dan demi Akta Kelahiran anak
        tersebut sepanjang yang berhubungan perkara ini sesuai dengan Hukum Acara Perdata Tingkat pertama.
    </p>

    <table class="cols" style="margin-top: 26pt;">
        <tr>
            <td></td>
            <td>{{ $signature['city'] }}, {{ $date }}</td>
        </tr>
    </table>
    <table class="cols" style="margin-top: 4pt;">
        <tr>
            <td>
                Yang Diberi Kuasa
                <div class="space"></div>
                <span class="line">{{ $grantee['name'] }}</span>
            </td>
            <td>
                Yang Memberi Kuasa
                <div class="space"></div>
                <span class="line">{{ $grantor['name'] }}</span>
            </td>
        </tr>
    </table>

    {{-- Blok "Mengetahui": judul rata tengah di atas baris a.n LURAH, sisanya rata kiri --}}
    <table class="know-wrap" style="margin-top: 22pt;">
        <tr>
            <td class="know-offset"></td>
            <td>
                <table class="know">
                    <tr><td class="know-head">Mengetahui</td></tr>
                    @foreach ($signature['prefix'] as $line)
                        <tr><td>{{ $line }}</td></tr>
                    @endforeach
                    <tr><td>{{ $signature['position'] }}</td></tr>
                    <tr><td class="know-space"></td></tr>
                    <tr><td>{{ $signature['name'] }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>