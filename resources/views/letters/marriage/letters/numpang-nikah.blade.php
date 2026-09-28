@use('App\Support\PersonRows')
@php $pp = 'letters.marriage.partials.'; $m = $marriage; @endphp
@include($pp.'office-header')

<div class="judul u">SURAT KETERANGAN NUMPANG NIKAH</div>
<div class="nomor">{{ $m['numpang']['letter_number'] ?? $m['letter_number'] }}</div>

<p>Bersama ini kami sampaikan, bahwa warga kami tersebut dibawah ini :</p>
@include($pp.'rows', ['bold' => false, 'rows' => PersonRows::make($m['bride'], 'Bin/Binti', education: 'after')])

<p class="bold">Akan melangsungkan pernikahan dengan seorang laki-laki dibawah ini :</p>
@include($pp.'rows', ['bold' => false, 'rows' => PersonRows::make($m['groom'], 'Bin')])

<p>Demikian surat keterangan ini di buat berdasarkan data yang sebenarnya.<br>
Atas perhatiannya kami ucapkan terima kasih.</p>

@include($pp.'signature', ['dateLong' => $m['numpang']['date']])