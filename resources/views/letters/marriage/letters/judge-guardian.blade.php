@use('App\Support\PersonRows')
@php $pp = 'letters.marriage.partials.'; $m = $marriage; @endphp
@include($pp.'office-header')

<div class="judul u">SURAT KETERANGAN WALI HAKIM</div>
<div class="nomor">Nomor: {{ $m['letter_number'] }}</div>

<p>Yang bertanda tangan di bawah ini menerangkan bahwa <b>akad nikah seorang wanita</b>:</p>
@include($pp.'rows', ['rows' => PersonRows::make($m['bride'], 'Binti')])

<p>Dengan seorang <b>calon mempelai laki-laki</b>:</p>
@include($pp.'rows', ['rows' => PersonRows::make($m['groom'], 'Bin')])

<p>Dilangsungkan secara <b>WALI HAKIM</b>.<br>
Adapun sebab dilangsungkannya dengan Wali Hakim karena:<br>
<b>{{ mb_strtoupper($m['judge_guardian_reason'] ?? '') }}</b></p>

<p>Demikian surat keterangan ini dibuat untuk digunakan seperlunya.</p>
@include($pp.'signature')