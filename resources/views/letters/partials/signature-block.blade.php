{{--
    BLOK TANDA TANGAN PEJABAT TERPADU
    ---------------------------------
    Mengikuti $signature dari LetterPdfService, sehingga satu blok ini cocok untuk
    semua kombinasi penandatangan:
      - Lurah                         : prefix = [],                                   position = 'Lurah ...'
      - a.n. Lurah u.b. Carik/Kaur    : prefix = ['a.n. Lurah ...', 'u.b.'],           position = 'Carik'
      - Panewu / pejabat lain         : prefix = [],                                   position = 'Panewu Ngemplak'
    Kop 'lurah' / 'pemerintah' tidak mengubah isi blok ini; yang menentukan adalah $signature,
    kecuali jabatan kosong -> diisi default sesuai varian kop.

    Opsi (lewat @include(..., [...])):
      'dateKey' => 'date' | 'date_long'   (default: 'date_long' bila section date_long ada)
--}}
@php ob_start(); @endphp@yield('kop_variant')@php $sigVariantSection = trim(ob_get_clean()); @endphp
@php $sigHasDateLong = false; @endphp
@hasSection('date_long')
    @php $sigHasDateLong = true; @endphp
@endif
@php
    $sigVariant = $sigVariantSection
        ?: ($kop['variant'] ?? (($kop['is_lurah'] ?? false) ? 'lurah' : 'pemerintah'));
    $sigDateKey = $dateKey ?? ($sigHasDateLong ? 'date_long' : 'date');
    $sigPosition = ($signature['position'] ?? '') !== ''
        ? $signature['position']
        : ($sigVariant === 'lurah' ? 'Lurah Bimomartani' : 'Lurah');
@endphp
<table class="ttd">
    <tr>
        <td class="ttd-spacer"></td>
        <td class="ttd-content">
            <div>{{ $signature['city'] ?? 'Bimomartani' }}, {{ $signature[$sigDateKey] ?? ($signature['date'] ?? '') }}</div>
            @foreach (($signature['prefix'] ?? []) as $line)
                <div>{{ $line }}</div>
            @endforeach
            <div>{{ $sigPosition }}</div>
            <div class="ttd-space">
                @include('letters.partials.signature-image')
            </div>
            <div>{{ $signature['name'] ?? '' }}</div>
        </td>
    </tr>
</table>
