@extends('letters.death.layout')

@section('title', 'SPTJM Kebenaran Data Kematian')
@section('content')
<div class="legacy-head"><div class="main">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK (SPTJM)</div><div class="main">KEBENARAN DATA KEMATIAN</div></div>
<p style="margin-top:13pt">Yang bertanda tangan dibawah ini saya</p>
<table class="legacy-table compact">
    <tr><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['statement']['nik'] ?? '' }}</td></tr>
    <tr><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['statement']['name'] ?? '' }}</td></tr>
    <tr><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write"></td></tr>
    <tr><td>Umur</td><td>:</td><td class="write"></td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td class="write"></td></tr>
    <tr><td>Alamat</td><td>:</td><td class="write">{{ $death['statement']['address'] ?? '' }}</td></tr>
</table>
<p style="margin-top:9pt">Dengan ini Menyatakan dan melaporkan dengan sebenar-benarnya bahwa</p>
<table class="legacy-table compact">
    <tr><td class="label">Nama Lengkap</td><td class="colon">:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr>
    <tr><td>NIK</td><td>:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr>
    <tr><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write">{{ $death['deceased']['birth_place_date'] ?? '' }}</td></tr>
    <tr><td>Telah Meninggal Dunia Pada</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }}</td></tr>
    <tr><td>Hari/Tanggal</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }}</td></tr>
    <tr><td>Jam</td><td>:</td><td class="write">{{ $death['deceased']['death_time'] ?? '' }} WIB</td></tr>
    <tr><td>Tempat Meninggal</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }}</td></tr>
</table>
<p style="text-align:justify;margin-top:6pt">Demikian Surat Pernyataan saya ini saya buat dengan sebenar-benarnya, dan apabila dikemudian hari ternyata pernyataan saya ini tidak benar, saya bersedia di proses secara hukum sesuai dengan peraturan perundang-undangan yang diterbitkan akibat dari pernyataan ini tidak saya.</p>
<div class="right" style="margin-top:5pt">Sleman &nbsp;&nbsp;&nbsp;&nbsp; {{ $signature['date'] ?? '' }}<br>Yang menyatakan</div>
<table class="signature-lines" style="margin-top:2pt"><tr><td>Saksi II<div class="space"></div><strong><u></u></strong><br>NIK : ____________________</td><td>Saksi I<div class="space"></div><strong><u></u></strong><br>NIK : ____________________</td></tr></table>
<table class="signature-lines" style="margin-top:10pt"><tr><td>Ketua RT<div class="space"></div></td><td>Ketua RW<div class="space"></div></td></tr><tr><td colspan="2">Mengetahui<br><strong>a.n LURAH BIMOMARTANI</strong><div class="space"></div><strong><u>{{ $signature['name'] ?? '' }}</u></strong></td></tr></table>
@endsection
