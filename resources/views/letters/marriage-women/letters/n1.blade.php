@use('App\Support\PersonRows')
@php $pp = 'letters.marriage-women.partials.'; $m = $marriage; $b = $m['bride']; @endphp
@include($pp.'model-header', ['lampiran' => 'V', 'model' => 'N1'])
@include($pp.'office-header')

<div class="judul u">PENGANTAR NIKAH</div>
<div class="nomor">Nomor: {{ $m['letter_number'] }}</div>

<p>Yang bertanda tangan di bawah ini menjelaskan dengan sesungguhnya bahwa :</p>
@include($pp.'rows', ['rows' => PersonRows::make($b, false, gender: true, education: 'before', nameLabel: 'Nama')])

<table class="rows" style="margin-top:8pt">
    <tr><td class="no">10.</td><td colspan="3">Status pernikahan</td></tr>
    <tr><td class="no"></td><td class="lbl">a. Laki-laki: Jejaka, Duda, atau beristri ke .....</td><td class="sep">:</td><td>&nbsp;</td></tr>
    <tr><td class="no"></td><td class="lbl">b. Perempuan: Perawan, atau janda</td><td class="sep">:</td><td class="bold">{{ mb_strtoupper($b['status'] ?? '') }}</td></tr>
    <tr><td class="no">11.</td><td class="lbl">Nama isteri/suami terdahulu</td><td class="sep">:</td><td>{{ filled($m['ex_husband']['name']) ? mb_strtoupper($m['ex_husband']['name']) : '.' }}</td></tr>
</table>

<p>Adalah benar anak dari pernikahan seorang pria :</p>
@include($pp.'rows', ['marker' => 'bullet', 'rows' => PersonRows::make($m['bride_father'], false)])
<p>dengan seorang wanita :</p>
@include($pp.'rows', ['marker' => 'bullet', 'rows' => PersonRows::make($m['bride_mother'], false)])

<p>Demikian, surat Pengantar ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.</p>
@include($pp.'signature')