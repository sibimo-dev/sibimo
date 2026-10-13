{{--
    Surat Pernyataan Tanggung Jawab Mutlak Kebenaran sebagai Pasangan Suami Istri.
    Standalone (tanpa kop). Font Times New Roman. Blok "Mengetahui" (rata kiri di dalam
    blok, ditulis langsung di bawah).
--}}
@php
    $relationship = data_get($form, 'family_relationship') ?: 'Suami/Istri/Anak *)';
    $witnessOne = data_get($marriage_witnesses, 0, []);
    $witnessTwo = data_get($marriage_witnesses, 1, []);

    $personRows = fn (array $p) => [
        'Nama' => $p['name'] ?? null,
        'NIK' => $p['nik'] ?? null,
        'Tempat/Tanggal Lahir' => $p['birth'] ?? null,
        'Pekerjaan' => $p['occupation'] ?? null,
        'Alamat' => $p['address'] ?? null,
    ];

    $blocks = [
        ['title' => 'Saya yang bertanda tangan dibawah ini', 'rows' => $personRows($applicant)],
        ['title' => 'Menyatakan bahwa', 'rows' => $personRows($husband)],
        ['title' => 'Adalah Suami/ Istri *) dari :', 'rows' => $personRows($wife)],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Tanggung Jawab Mutlak</title>
    <style>
        @page { margin: 2cm; 1cm; 2cm; 1cm; }
        body { margin: 0; font-family: "Times New Roman", Times, serif; font-size: 11pt; line-height: 1.3; color: #000; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20pt; }
        .sec { font-weight: normal; margin-top: 8pt; }
        p { margin: 0; }
        table { border-collapse: collapse; }
        table.person { width: 100%; }
        table.person td { padding: 1pt 0; vertical-align: top; }
        table.person td.lbl { width: 110pt; padding-left: 14pt; }
        table.person td.sep { width: 10pt; }
        .para { margin-top: 8pt; text-align: justify; }
        table.cols { width: 100%; }
        table.cols td { width: 50%; vertical-align: top; text-align: center; }
        .space { height: 40pt; }
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
    <div class="title">
        SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK<br>
        KEBENARAN SEBAGAI PASANGAN SUAMI ISTRI
    </div>

    @foreach ($blocks as $i => $block)
        <p class="sec">{{ $block['title'] }}</p>
        <table class="person">
            @foreach ($block['rows'] as $label => $value)
                <tr>
                    <td class="lbl">{{ $label }}</td>
                    <td class="sep">:</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>
        @if ($i === 0)
            <p style="margin-top: 2pt;">Status Hubungan Keluarga : {{ $relationship }}</p>
        @endif
    @endforeach

    <p class="para">
        Sebagaimana tercantum dalam kartu keluarga ( KK ) Nomor {{ $applicant['kk_number'] ?? '' }}<br>
        Yang perkawinannya belum tercatat sesuai peraturan perundang – undangan pada<br>
        ( sesuai UU perkawinan No 1 Th 1974 )
    </p>

    <p class="para">
        Demikian surat pernyataan ini saya buat dengan sebenar-benarnya dan apabila dikemudian hari
        ternyata pernyataan ini tidak benar, maka saya bersedia diproses secara hukum sesuai dengan
        peraturan perundang-undangan dan dokumen yang diterbitkan akibat pernyataan ini menjadi tidak syah.
    </p>

    <table class="cols" style="margin-top: 10pt;">
        <tr>
            <td></td>
            <td>
                {{ $signature['city'] }}, {{ $signature['date_long'] }}<br>
                Saya Yang Menyatakan
                <div class="space"></div>
                <span class="line">{{ $applicant['name'] ?? '' }}</span>
            </td>
        </tr>
    </table>

    <table class="cols" style="margin-top: 16pt; page-break-inside: avoid;">
        <tr>
            <td>
                Saksi I
                <div class="space"></div>
                <span class="line">{{ $witnessOne['name'] ?? '' }}</span><br>
                NIK : {{ $witnessOne['nik'] ?? '' }}
            </td>
            <td>
                Saksi II
                <div class="space"></div>
                <span class="line">{{ $witnessTwo['name'] ?? '' }}</span><br>
                NIK : {{ $witnessTwo['nik'] ?? '' }}
            </td>
        </tr>
    </table>

    {{-- Blok "Mengetahui": judul rata tengah di atas baris a.n LURAH, sisanya rata kiri --}}
    <table class="know-wrap" style="margin-top: 20pt;">
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