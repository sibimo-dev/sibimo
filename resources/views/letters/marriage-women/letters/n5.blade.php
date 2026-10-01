@use('App\Support\PersonRows')
@php $pp = 'letters.marriage-women.partials.'; $m = $marriage; @endphp
@include($pp.'model-header', ['lampiran' => 'IX', 'model' => 'N5'])

<div class="judul u">SURAT IZIN ORANG TUA</div>
<p>Yang bertanda tangan di bawah ini :</p>
@include($pp.'rows', ['tag' => 'A.', 'rows' => PersonRows::make($m['bride_father'], 'Bin')])
@include($pp.'rows', ['tag' => 'B.', 'rows' => PersonRows::make($m['bride_mother'], 'Binti')])

<p>Adalah ayah dan ibu kandung/wali/pengampu dari :</p>
@include($pp.'rows', ['tag' => '', 'rows' => PersonRows::make($m['bride'], 'Bin/Binti')])
<p>Memberikan izin kepada anak kami untuk melakukan pernikahan dengan:</p>
@include($pp.'rows', ['tag' => '', 'rows' => PersonRows::make($m['groom'], 'Bin/Binti')])

<p style="margin-top:10pt">Demikian surat izin ini dibuat dengan kesadaran tanpa ada paksaan dari siapapun dan untuk digunakan seperlunya.</p>

<table class="sign" style="margin-top:16pt">
    <tr>
        <td class="sign-l">&nbsp;</td>
        <td class="sign-r" style="text-align:left; padding-left:130pt; white-space:nowrap">{{ $signature['city'] }}, {{ $signature['date_long'] }}</td>
    </tr>
    <tr>
        <td class="sign-l">Ayah/wali/pengampu</td>
        <td class="sign-r" style="text-align:left; padding-left:130pt">Ibu/wali/pengampu</td>
    </tr>
    <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    <tr>
        <td class="sign-l bold">{{ mb_strtoupper($m['bride_father']['name'] ?? '') }}</td>
        <td class="sign-r bold" style="text-align:left; padding-left:130pt">{{ mb_strtoupper($m['bride_mother']['name'] ?? '') }}</td>
    </tr>
</table>