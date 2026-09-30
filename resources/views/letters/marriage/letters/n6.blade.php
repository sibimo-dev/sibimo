@push('styles')
    .n6-rows td.tag { font-weight: normal; }
    .n6-rows td.lbl { width: 190pt; }
@endpush
@use('App\Support\PersonRows')
@php $pp = 'letters.marriage.partials.'; $m = $marriage; $ex = $m['ex_husband']; @endphp
@include($pp.'model-header', ['lampiran' => 'X', 'model' => 'N6'])
@include($pp.'office-header')

<div class="judul" style="text-align:center; font-weight:normal; margin-top:8pt">SURAT KETERANGAN KEMATIAN</div>
<div class="nomor" style="text-align:center">Nomor: {{ $m['letter_number'] }}</div>

<p>Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa :</p>

<div class="n6-rows">
    @include($pp.'rows', ['tag' => 'A', 'bold' => false, 'rows' => PersonRows::make($ex, 'Bin')])
    @include($pp.'rows', ['tag' => '', 'marker' => 'none', 'bold' => false, 'rows' => [
        'Telah meninggal dunia pada tanggal' => $ex['died_at'],
        'Di' => $ex['died_place'],
    ]])

    <p>Yang bersangkutan adalah Suami / Istri dari :</p>
    @include($pp.'rows', ['tag' => 'B', 'bold' => false, 'rows' => PersonRows::make($m['bride'], 'Binti')])
</div>

<p>Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk dapat digunakan seperlunya.</p>

@include($pp.'signature')