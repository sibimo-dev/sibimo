@use('App\Support\PersonRows')
@push('styles')
    .n4 { font-size: 11.5pt; }
@endpush
@php $pp = 'letters.marriage-women.partials.'; $m = $marriage; @endphp
<div class="n4">
@include($pp.'model-header', ['lampiran' => 'VIII', 'model' => 'N4'])

<div class="judul" style="margin-top:6pt">PERSETUJUAN CALON PENGANTIN</div>
<p style="margin-top:18pt">Yang bertanda tangan di bawah ini :</p>

<p class style="margin-top:14pt">A. Calon Suami :</p>
@include($pp.'rows', ['bold' => false, 'rows' => PersonRows::make($m['groom'], 'Bin')])
<p class style="margin-top:20pt">B. Calon Istri :</p>
@include($pp.'rows', ['bold' => false, 'rows' => PersonRows::make($m['bride'], 'Binti')])

<p class style="margin-top:22pt">Menyatakan dengan sesungguhnya bahwa atas dasar suka rela, dengan kesadaran sendiri, tanpa ada paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan.</p>
<p class style="margin-top:8pt">Demikian surat persetujuan ini dibuat untuk digunakan seperlunya.</p>

<table class="sign" style="margin-top:16pt">
    <tr>
        <td class="sign-l">&nbsp;</td>
        <td class="sign-r" style="text-align:left; padding-left:130pt; padding-right:0; white-space:nowrap">{{ $signature['city'] }}, {{ $signature['date_long'] }}</td>
    </tr>
    <tr>
        <td class="sign-l">Calon Suami</td>
        <td class="sign-r" style="text-align:left; padding-left:130pt; padding-right:0">Calon Istri</td>
    </tr>
    <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    <tr>
        <td class="sign-l bold">{{ mb_strtoupper($m['groom']['name'] ?? '') }}</td>
        <td class="sign-r bold" style="text-align:left; padding-left:130pt; padding-right:0">{{ mb_strtoupper($m['bride']['name'] ?? '') }}</td>
    </tr>
</table>
</div>