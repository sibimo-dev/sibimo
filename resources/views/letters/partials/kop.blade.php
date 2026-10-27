{{--
    KOP SURAT TERPADU
    -----------------
    Satu partial untuk semua surat. Dua varian kop Kalurahan:
      - 'pemerintah' : Kop Pemerintah Kalurahan (penandatangan: Lurah / a.n. Lurah u.b. Carik / dst.)
      - 'lurah'      : Kop Lurah / Kalurahan (aksara lebih ringkas, baris ke-3 lebih pendek)
    Dan satu varian Dukcapil (type = 'dukcapil', tidak berubah).

    Cara memilih varian (urutan prioritas):
      1. @section('kop_variant', 'pemerintah|lurah') di surat itu sendiri
      2. $kop['variant']  ('pemerintah' | 'lurah')   -> dari LetterPdfService
      3. $kop['is_lurah'] (bool, kompatibel dengan kode lama)
      4. default: 'pemerintah'

    Surat apa pun (keterangan, pernyataan, form, dll.) cukup memanggil:
        @include('letters.partials.kop')
    CSS-nya ada di letters.partials.kop-styles.
    Pakai @section('hide_aksara') bila aksara Jawa ingin disembunyikan.
--}}
@php ob_start(); @endphp@yield('kop_variant')@php $kopVariantSection = trim(ob_get_clean()); @endphp
@php
    $kopVariant = $kopVariantSection
        ?: ($kop['variant'] ?? (($kop['is_lurah'] ?? false) ? 'lurah' : 'pemerintah'));
    $kopVariant = $kopVariant === 'lurah' ? 'lurah' : 'pemerintah';
    $kopLine1 = $kop['line1'] ?? 'PEMERINTAH KABUPATEN SLEMAN';
    $kopLine2 = $kop['line2'] ?? 'KAPANEWON NGEMPLAK';
    $kopLine3 = $kop['line3'] ?? ($kopVariant === 'lurah' ? 'KALURAHAN BIMOMARTANI' : 'PEMERINTAH KALURAHAN BIMOMARTANI');
@endphp
@if (($kop['type'] ?? 'kalurahan') === 'dukcapil')
    <table class="kop kop-dukcapil">
        <tr>
            <td class="kop-logo">
                <img src="{{ $logo ?? '' }}" alt="Logo Kabupaten Sleman">
            </td>
            <td class="kop-text">
                <div class="kopd-1">{{ $kop['line1'] ?? '' }}</div>
                <div class="kopd-2">{{ $kop['line2'] ?? '' }}</div>
                <div class="kopd-info">{{ $kop['address'] ?? '' }}</div>
                <div class="kopd-info">{{ $kop['contact'] ?? '' }}</div>
                <div class="kopd-info">{{ $kop['web_email'] ?? '' }}</div>
            </td>
        </tr>
    </table>
    <div class="kop-line dukcapil"></div>
@else
    <table class="kop kop-{{ $kopVariant }}">
        <tr>
            <td class="kop-logo">
                <img src="{{ $logo ?? '' }}" alt="Logo Kabupaten Sleman">
            </td>
            <td class="kop-text">
                <div class="kop-1">{{ $kopLine1 }}</div>
                <div class="kop-2">{{ $kopLine2 }}</div>
                <div class="kop-3">{{ $kopLine3 }}</div>
                @hasSection('hide_aksara')
                @else
                    @if (!empty($kop['aksara']))
                        <div class="kop-aksara {{ $kopVariant === 'lurah' ? 'lurah' : '' }}"><img src="{{ $kop['aksara'] }}" alt=""></div>
                    @endif
                @endif
                <div class="kop-info">{{ $kop['address'] ?? '' }}</div>
                <div class="kop-info">{{ $kop['contact'] ?? '' }}</div>
                @if (!empty($kop['email']))
                    <div class="kop-email">Email : {{ $kop['email'] }}</div>
                @endif
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>
@endif
