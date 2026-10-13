@extends('letters.death.layout')

@section('title', 'Surat Keterangan')
@section('content')
<div class="title">Surat Keterangan</div>
<div class="number">NOMOR : {{ $number ?? '' }}</div>
<p style="margin:14pt 0 5pt 75pt">Yang bertanda tangan dibawah ini</p>
<table class="legacy-table" style="margin-left:75pt;width:80%"><tr><td class="label">Nama</td><td class="colon">:</td><td class="write">{{ $signature['name'] ?? '' }}</td></tr><tr><td>Jabatan</td><td>:</td><td class="write">{{ $signature['position'] ?? '' }}</td></tr></table>
<p style="margin:14pt 0 5pt 75pt">Dengan ini menerangkan bahwa</p>
<table class="legacy-table" style="margin-left:75pt;width:80%">
    <tr><td class="label">Nama</td><td class="colon">:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr>
    <tr><td>Tempat/Tgl. Lahir</td><td>:</td><td class="write">{{ $death['reporter']['birth_place_date'] ?? '' }}</td></tr>
    <tr><td>NIK</td><td>:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td class="write">{{ $death['reporter']['gender'] ?? '' }}</td></tr>
    <tr><td>Status Perkawinan</td><td>:</td><td class="write"></td></tr>
    <tr><td>Agama</td><td>:</td><td class="write">{{ $death['reporter']['religion'] ?? '' }}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['reporter']['occupation'] ?? '' }}</td></tr>
    <tr><td>Alamat</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr>
    <tr><td>Pergi ke</td><td>:</td><td class="write"></td></tr>
    <tr><td>Keperluan</td><td>:</td><td class="write">Mohon Akta Kematian Untuk {{ $death['deceased']['name'] ?? '' }}</td></tr>
</table>
<p style="margin-top:4pt;text-align:justify">berhubung maksud yang bersangkutan, mohon yang berwenang memberikan bantuan serta fasilitas seperlunya</p>
<p style="margin-top:10pt">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
<table class="signature-lines"><tr><td></td><td>{{ $signature['city'] ?? 'Bimomartani' }} &nbsp;&nbsp; {{ $signature['date'] ?? '' }}<br>{{ $signature['prefix'][0] ?? '' }}<br>{{ $signature['position'] ?? '' }}<div class="long-space"></div><strong><u>{{ $signature['name'] ?? '' }}</u></strong></td></tr></table>
@endsection
