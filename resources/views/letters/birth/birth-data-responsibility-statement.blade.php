{{--
    Surat Pernyataan Tanggung Jawab Mutlak Kebenaran Data Kelahiran.
    Path: resources/views/letters/birth/birth-data-responsibility-statement.blade.php

    Expected variables:
      $applicant        : nik, name, birth, occupation, address
      $child            : name, nik, birth, occupation, address
      $mother           : name, nik, birth, occupation, address
      $father           : name, nik, birth, occupation, address
      $birth_attendant  : name, nik, birth, occupation, address
      $witnesses        : array of 2 items (name, nik)
      $signature        : city, name (name of the Ulu-Ulu official)
--}}
@php
    $witnessOne = data_get($witnesses, 0, []);
    $witnessTwo = data_get($witnesses, 1, []);

    // Row order follows the paper form: the applicant block starts with NIK, the others start with Nama.
    $personRows = function (array $person, bool $nikFirst = false, string $nameLabel = 'Nama') {
        $name = [$nameLabel => $person['name'] ?? null];
        $nik = ['NIK' => $person['nik'] ?? null];

        return array_merge(
            $nikFirst ? $nik + $name : $name + $nik,
            [
                'Tempat/Tanggal Lahir' => $person['birth'] ?? null,
                'Pekerjaan' => $person['occupation'] ?? null,
                'Alamat' => $person['address'] ?? null,
            ]
        );
    };

    $blocks = [
        [
            'title' => 'Saya yang bertanda tangan dibawah ini',
            'title_center' => false,
            'rows' => $personRows($applicant ?? [], true),
        ],
        [
            'title' => 'Menyatakan bahwa',
            'title_center' => true,
            'rows' => $personRows($child ?? []),
        ],
        [
            'title' => 'Adalah anak kandung dari :',
            'title_center' => true,
            'rows' => $personRows($mother ?? [], false, 'Nama Ibu'),
        ],
        [
            'title' => null,
            'title_center' => false,
            'rows' => $personRows($father ?? [], false, 'Nama Ayah'),
        ],
        [
            'title' => 'yang lahir dengan penolong kelahiran',
            'title_center' => false,
            'rows' => $personRows($birth_attendant ?? []),
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Tanggung Jawab Mutlak Kebenaran Data Kelahiran</title>
    <style>
        @page { margin: 1cm 1.5cm 1cm 1.5cm; }
        body { margin: 0; font-family: "Times New Roman", Times, serif; font-size: 10pt; line-height: 1.2; color: #000; }
        p { margin: 0; }
        table { border-collapse: collapse; }

        .title { text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 8pt; }

        .block { margin-top: 4pt; page-break-inside: avoid; }
        .block-title { margin-bottom: 0; }
        .block-title.center { text-align: center; font-weight: bold; }

        table.person { width: 100%; }
        table.person td { padding: 0; vertical-align: top; }
        table.person td.lbl { width: 110pt; padding-left: 14pt; }
        table.person td.sep { width: 10pt; }

        .para { margin-top: 6pt; text-align: justify; }

        table.cols { width: 100%; }
        table.cols td { width: 50%; vertical-align: top; text-align: center; }
        .space { height: 26pt; }
        .date-blank { display: inline-block; min-width: 90pt; border-bottom: 1px dotted #000; }
        .line { display: inline-block; min-width: 130pt; border-bottom: 1px dashed #000; }
        .witness { display: inline-block; text-align: left; }
        .witness .line { display: block; min-width: 170pt; text-align: center; }

        table.know-wrap { width: 100%; page-break-inside: avoid; }
        table.know-wrap td { padding: 0; vertical-align: top; }
        td.know-offset { width: 33%; }
        table.know td { padding: 0; text-align: left; }
        table.know td.know-head { text-align: center; }
        table.know td.know-space { height: 30pt; }
    </style>
</head>
<body>
    <div class="title">
        SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK<br>
        KEBENARAN DATA KELAHIRAN
    </div>

    @foreach ($blocks as $block)
        <div class="block">
            @if ($block['title'])
                <p class="block-title {{ $block['title_center'] ? 'center' : '' }}">{{ $block['title'] }}</p>
            @endif
            <table class="person">
                @foreach ($block['rows'] as $label => $value)
                    <tr>
                        <td class="lbl">{{ $label }}</td>
                        <td class="sep">:</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach

    <p class="para">
        Demikian surat pernyataan ini saya buat dengan sebenar-benarnya dan apabila dikemudian hari
        ternyata pernyataan ini tidak benar, maka saya bersedia diproses secara hukum sesuai dengan
        peraturan perundang-undangan dan dokumen yang diterbitkan akibat pernyataan ini menjadi tidak sah.
    </p>

    <table class="cols" style="margin-top: 6pt;">
        <tr>
            <td></td>
            <td>
                {{ $signature['city'] ?? '' }}, <span class="date-blank"></span><br>
                Saya yang Menyatakan
                <div class="space"></div>
                <span class="line">{{ $applicant['name'] ?? '' }}</span>
            </td>
        </tr>
    </table>

    <table class="cols" style="margin-top: 6pt; page-break-inside: avoid;">
        <tr>
            <td>
                Saksi I
                <div class="space"></div>
                <div class="witness">
                    <span class="line">{{ $witnessOne['name'] ?? '' }}</span>
                    NIK : {{ $witnessOne['nik'] ?? '' }}
                </div>
            </td>
            <td>
                Saksi II
                <div class="space"></div>
                <div class="witness">
                    <span class="line">{{ $witnessTwo['name'] ?? '' }}</span>
                    NIK : {{ $witnessTwo['nik'] ?? '' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- "Mengetahui" block: heading centered above the "a.n LURAH" line, the rest left-aligned --}}
    <table class="know-wrap" style="margin-top: 8pt;">
        <tr>
            <td class="know-offset"></td>
            <td>
                <table class="know">
                    <tr><td class="know-head">Mengetahui</td></tr>
                    <tr><td>a.n LURAH BIMOMARTANI</td></tr>
                    <tr><td>Ulu-Ulu</td></tr>
                    <tr><td class="know-space"></td></tr>
                    <tr><td>{{ $signature['name'] ?? '' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>