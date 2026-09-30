@push('styles')
    .n2 { font-size: 11.5pt; }
@endpush
<div class="n2">
@php $pp = 'letters.marriage.partials.'; $m = $marriage; @endphp
@include($pp.'model-header', ['lampiran' => 'VI', 'model' => 'N2'])

<div class="model" style="margin-top:0">{{ $signature['city'] }}, {{ $signature['date_long'] }}</div>
<p class="bold" style="margin-top:12pt">Perihal : Permohonan Kehendak Nikah</p>

<p style="margin-top:10pt">Kepada Yth.<br><b>Kepala KUA Kecamatan<br>Di {{ ucwords(strtolower($region['district_name'])) }}</b></p>

<p style="margin-top:12pt">Assalamu'alaikum wr. wb<br>
Dengan hormat kami mengajukan permohonan kehendak nikah untuk atas nama :</p>
<table class="rows" style="margin-left:0; margin-top:4pt">
    <tr><td class="lbl" style="width:110pt">Calon suami</td><td class="sep">:</td><td>{{ mb_strtoupper($m['groom']['name'] ?? '') }}</td></tr>
    <tr><td class="lbl" style="width:110pt">Calon istri</td><td class="sep">:</td><td>{{ mb_strtoupper($m['bride']['name'] ?? '') }}</td></tr>
    <tr>
        <td class="lbl" style="width:110pt">Hari/tanggal/jam</td>
        <td class="sep">:</td>
        <td style="width:220pt">{{ collect([$m['akad']['day'], $m['akad']['date']])->filter()->implode(', ') }}</td>
        <td>jam: {{ $m['akad']['time'] }}</td>
    </tr>
    <tr><td class="lbl" style="width:110pt">Tempat akad nikah</td><td class="sep">:</td><td>{{ $m['akad']['place'] }}</td></tr>
</table>

<p style="margin-top:14pt">Bersama ini kami sampaikan surat-surat yang diperlukan untuk diperiksa sebagai</p>
@foreach ([
    'Surat Pengantar nikah dari Desa/Kelurahan;',
    'Persetujuan Calon Mempelai;',
    'Fotocopy KTP;',
    'Fotocopy akte kelahiran;',
    'Fotocopy Kartu Keluarga;',
    'Pas poto 2x3 = 5 lembar, dan 4x6 = 1 lembar, berlatar belakang BIRU;',
    'Surat Keterangan Wali Nikah;',
    '................................',
] as $i => $doc)
    <div>{{ $i + 1 }}. {{ $doc }}</div>
@endforeach

<p style="margin-top:14pt">Demikian permohonan ini kami sampaikan, kiranya dapat diperiksa, dihadiri dan dicatat sesuai dengan ketentuan peraturan perundang-undangan.</p>

<table class="sign" style="margin-top:16pt">
    <tr>
        <td class="sign-l">Diterima tanggal: <span class="blank">&nbsp;</span><br>Yang menerima,<br>Kepala KUA/Penghulu</td>
        <td class="sign-r">Wassalam,<br>Pemohon</td>
    </tr>
    <tr><td colspan="2" class="sign-space">&nbsp;</td></tr>
    <tr>
        <td class="sign-l"><span class="blank">&nbsp;</span></td>
        <td class="sign-r">{{ mb_strtoupper($m['bride']['name'] ?? '') }}</td>
    </tr>
</table>
</div>