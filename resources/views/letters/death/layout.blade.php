<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', $title ?? 'Surat Kematian')</title>
    <style>
        @page { margin: 0.75cm 1.15cm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #000; font-family: Arial, Helvetica, sans-serif; font-size: 9pt; line-height: 1.13; }
        table { border-collapse: collapse; }
        p { margin: 0 0 5pt; }
        .kop { width: 100%; }
        .kop td { padding: 0; vertical-align: top; }
        .kop-logo { width: 63pt; padding-top: 2pt !important; }
        .kop-logo img { width: 68pt; height: 90pt; }
        .kop-text { text-align: center; padding-top: 3pt !important; }
        .kop-1 { font-size: 12pt; font-weight: bold; }
        .kop-2 { font-size: 14pt; font-weight: bold; }
        .kop-3 { font-size: 15.5pt; font-weight: bold; }
        .aksara { height: 32pt; margin: -1pt 0; }
        .aksara img { width: 235pt; max-height: 32pt; }
        .kop-info { font-size: 9.2pt; font-weight: bold; line-height: 1.05; }
        .kop-line { border-bottom: 2pt solid #000; margin: 5pt 0 8pt; }
        .title { text-align: center; font-size: 11pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin: 4pt 0 2pt; }
        .subtitle { text-align: center; margin-bottom: 8pt; }
        .number { text-align: center; font-weight: bold; margin-bottom: 8pt; }
        .section-title { font-weight: bold; margin: 7pt 0 3pt; text-transform: uppercase; }
        .intro { text-align: justify; }
        .data { width: 100%; }
        .data td { padding: 1.5pt 1pt; vertical-align: top; }
        .data .no { width: 18pt; }
        .data .label { width: 145pt; }
        .data .sep { width: 10pt; }
        .data .value { border-bottom: 1px dotted #000; min-width: 90pt; height: 15pt; }
        .two-col { width: 100%; }
        .two-col > tbody > tr > td { width: 50%; vertical-align: top; padding-right: 8pt; }
        .two-col > tbody > tr > td + td { padding: 0 0 0 8pt; }
        .box { border: 1.3pt solid #000; padding: 4pt; margin-top: 4pt; }
        .grid { width: 100%; }
        .grid th, .grid td { border: 1px solid #000; padding: 2.5pt 3pt; vertical-align: top; }
        .grid th { text-align: center; font-weight: bold; }
        .small { font-size: 8pt; }
        .center { text-align: center; }
        .right { text-align: right; }
        .signature { width: 100%; margin-top: 18pt; page-break-inside: avoid; }
        .signature td { width: 50%; text-align: center; vertical-align: top; }
        .signature-space { height: 48pt; }
        .line { display: inline-block; min-width: 145pt; border-bottom: 1px solid #000; height: 14pt; }
        .page-break { page-break-before: always; }
        .check { display: inline-block; border: 1px solid #000; width: 10pt; height: 10pt; margin-right: 4pt; vertical-align: -1pt; }
        .blank { height: 18pt; border-bottom: 1px dotted #000; }
        .legacy-head { text-align: center; font-weight: bold; line-height: 1.08; }
        .legacy-head .main { font-size: 12pt; }
        .legacy-head .sub { font-size: 10pt; }
        .legacy-head .code { font-size: 8.5pt; text-align: right; border: 1px solid #000; padding: 3pt 8pt; width: 115pt; margin-left: auto; }
        .legacy-table { width: 100%; border-collapse: collapse; }
        .legacy-table td, .legacy-table th { padding: 1.5pt 3pt; vertical-align: top; }
        .legacy-table .label { width: 155pt; }
        .legacy-table .colon { width: 10pt; }
        .legacy-table .write { border-bottom: 1px dotted #000; min-width: 100pt; height: 14pt; }
        .legacy-section { border: 1.4pt solid #000; padding: 3pt 4pt; }
        .legacy-section + .legacy-section { border-top: 0; }
        .legacy-section-title { font-weight: bold; margin-bottom: 1pt; }
        .compact td { padding-top: 1pt; padding-bottom: 1pt; }
        .form-label { font-weight: bold; text-transform: uppercase; }
        .checklist { width: 100%; border-collapse: collapse; }
        .checklist td { padding: 1.5pt 2pt; vertical-align: top; }
        .checklist .check-box { width: 18pt; height: 18pt; border: 1px solid #000; }
        .signature-lines { width: 100%; border-collapse: collapse; margin-top: 12pt; }
        .signature-lines td { width: 50%; text-align: center; vertical-align: top; padding: 0 8pt; }
        .signature-lines .space { height: 40pt; }
        .signature-lines .long-space { height: 58pt; }
        @yield('extra_css')
    </style>
</head>
<body>
    @if ($include_kop ?? true)
        <table class="kop">
            <tr>
                <td class="kop-logo"><img src="{{ $logo ?? '' }}" alt="Logo Kabupaten Sleman"></td>
                <td class="kop-text">
                    <div class="kop-1">{{ $kop['line1'] ?? '' }}</div>
                    <div class="kop-2">{{ $kop['line2'] ?? '' }}</div>
                    <div class="kop-3">{{ $kop['line3'] ?? '' }}</div>
                    <div class="aksara"><img src="{{ $kop['aksara'] ?? '' }}" alt="Aksara Jawa"></div>
                    <div class="kop-info">{{ $kop['address'] ?? '' }}</div>
                    <div class="kop-info">{{ $kop['contact'] ?? '' }}</div>
                    <div class="kop-info">Email : {{ $kop['email'] ?? '' }}</div>
                </td>
            </tr>
        </table>
        <div class="kop-line"></div>
    @endif

    @yield('content')

    @if ($show_signature ?? false)
        <table class="signature">
            <tr>
                <td></td>
                <td>
                    {{ $signature['city'] ?? 'Bimomartani' }}, {{ $signature['date'] ?? '' }}<br>
                    @foreach (($signature['prefix'] ?? []) as $prefix)
                        {{ $prefix }}<br>
                    @endforeach
                    {{ $signature['position'] ?? '' }}
                    <div class="signature-space"></div>
                    <strong><u>{{ $signature['name'] ?? '' }}</u></strong>
                </td>
            </tr>
        </table>
    @endif
</body>
</html>
