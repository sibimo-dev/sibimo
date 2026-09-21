<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Surat')</title>
    <style>
        /* Margin halaman. Ukuran kertas diatur dari LetterPdfService::PAPER */
        @page { margin: 2cm 2cm 2cm 2cm; }

        body {
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11.4pt;
            line-height: 1.25;
            color: #000;
        }

        /* ===== KOP SURAT ===== */
        table.kop { width: 100%; border-collapse: collapse; }
        table.kop td { padding: 0; vertical-align: top; }
        table.kop td.kop-logo { width: 75pt; padding-top: 6pt; }
        td.kop-logo img { width: 63pt; height: 82pt; }
        table.kop td.kop-text { text-align: center; padding-top: 9pt; }
        .kop-1 { font-size: 13.3pt; font-weight: bold; line-height: 11.5pt; }
        .kop-2 { font-size: 15.2pt; font-weight: bold; line-height: 21pt; }
        .kop-3 { font-size: 17.1pt; font-weight: bold; line-height: 20pt; }
        .kop-aksara { margin-top: -2pt; margin-bottom: -3pt; }
        .kop-aksara img { width: 250pt; height: auto; }
        .kop-info { font-size: 11.4pt; font-weight: bold; line-height: 1.1; }
        .kop-email { font-size: 10.5pt; font-weight: bold; line-height: 1.1; }
        .kop-line { border-bottom: 2pt solid #000; margin-top: 18pt; }

        /* ===== JUDUL ===== */
        .judul {
            margin-top: 10pt;
            text-align: center;
            font-size: 13.3pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .nomor { text-align: center; font-weight: bold; margin-top: 3pt; }

        /* ===== ISI ===== */
        p { margin: 0; }
        p.gap-top { margin-top: 9pt; }
        p.gap-top-lg { margin-top: 18pt; }

        /* Tabel data "Label : Nilai" (menjorok 66pt, garis putus-putus di bawah nilai) */
        table.data {
            width: 388pt;
            margin-left: 66pt;
            border-collapse: collapse;
        }
        table.data td { padding: 2.5pt 0; vertical-align: top; }
        td.lbl { width: 111pt; }
        td.sep { width: 11pt; }
        td.val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 14pt; }

        /* ===== TANDA TANGAN ===== */
        .ttd { margin-top: 26pt; margin-left: 265pt; page-break-inside: avoid; }
        .ttd div { line-height: 20pt; }
        .ttd-space { height: 52pt; }

        @yield('extra_css')
    </style>
</head>
<body>

    {{-- ===== KOP ===== --}}
    <table class="kop">
        <tr>
            <td class="kop-logo">
                <img src="{{ $logo }}" alt="Logo Kabupaten Sleman">
            </td>
            <td class="kop-text">
                <div class="kop-1">{{ $kop['line1'] }}</div>
                <div class="kop-2">{{ $kop['line2'] }}</div>
                <div class="kop-3">{{ $kop['line3'] }}</div>
                @sectionMissing('hide_aksara')
                <div class="kop-aksara"><img src="{{ $kop['aksara'] }}" alt=""></div>
                @endif
                <div class="kop-info">{{ $kop['address'] }}</div>
                <div class="kop-info">{{ $kop['contact'] }}</div>
                @if ($kop['email'])
                    <div class="kop-email">Email : {{ $kop['email'] }}</div>
                @endif
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    {{-- ===== ISI SURAT (diisi tiap template) ===== --}}
    @yield('content')

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="ttd">
        <div>{{ $signature['city'] }}, @hasSection('date_long'){{ $signature['date_long'] }}@else{{ $signature['date'] }}@endif</div>
        @foreach ($signature['prefix'] as $line)
            <div>{{ $line }}</div>
        @endforeach
        <div>{{ $signature['position'] }}</div>
        <div class="ttd-space"></div>
        <div>{{ $signature['name'] }}</div>
    </div>

</body>
</html>