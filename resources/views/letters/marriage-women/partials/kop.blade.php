@once
    @push('styles')
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
        .kop-aksara.lurah img { width: 165pt; }
        .kop-info { font-size: 11.4pt; font-weight: bold; line-height: 1.1; }
        .kop-email { font-size: 10.5pt; font-weight: bold; line-height: 1.1; }
        .kop-line { border-bottom: 2pt solid #000; margin-top: 18pt; }
    @endpush
@endonce
<table class="kop">
    <tr>
        <td class="kop-logo">
            <img src="{{ $logo }}" alt="Logo Kabupaten Sleman">
        </td>
        <td class="kop-text">
            <div class="kop-1">{{ $kop['line1'] }}</div>
            <div class="kop-2">{{ $kop['line2'] }}</div>
            <div class="kop-3">{{ $kop['line3'] }}</div>
            <div class="kop-aksara {{ $kop['is_lurah'] ?? false ? 'lurah' : '' }}"><img src="{{ $kop['aksara'] }}" alt=""></div>
            <div class="kop-info">{{ $kop['address'] }}</div>
            <div class="kop-info">{{ $kop['contact'] }}</div>
            @if ($kop['email'])
                <div class="kop-email">Email : {{ $kop['email'] }}</div>
            @endif
        </td>
    </tr>
</table>
<div class="kop-line"></div>