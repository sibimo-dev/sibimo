@use('App\Support\PersonRows')
@php $pp = 'letters.marriage-women.partials.'; $m = $marriage; $g = $m['guardian']; @endphp
@include($pp.'office-header')

<div class="judul u">SURAT KETERANGAN WALI NIKAH</div>
<div class="nomor">Nomor: {{ $m['letter_number'] }}</div>

<p>Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa :</p>
@include($pp.'rows', ['rows' => PersonRows::make($g, 'Bin')])

<p>Adalah <b>Wali</b> yang berhak menikahkan <b>calon mempelai wanita</b>:</p>
@include($pp.'rows', ['rows' => PersonRows::make($m['bride'], 'Binti')])

<p>Yang akan menikah dengan <b>seorang laki-laki</b>:</p>
@include($pp.'rows', ['rows' => PersonRows::make($m['groom'], 'Bin')])

<p>Adapun hubungan <i>wali nasab</i> tersebut dengan calon mempelai wanita adalah sebagai:</p>
<p class="bold">{{ filled($g['relation']) ? mb_strtoupper($g['relation']) : '.' }}</p>
@if (strtoupper((string) $g['relation']) !== 'AYAH KANDUNG')
    <p>Karena {{ filled($g['reason']) ? $g['reason'] : '.' }}</p>
@endif

<p>Demikian surat keterangan ini dibuat untuk digunakan seperlunya.</p>
@include($pp.'signature')