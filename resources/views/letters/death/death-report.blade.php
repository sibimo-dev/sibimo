@extends('letters.death.layout')

@section('title', 'Laporan Kematian')
@section('content')
<div class="legacy-head"><div class="code">KODE F-2.28</div><div class="main" style="margin-top:8pt">LAPORAN KEMATIAN</div></div>
<p style="margin-top:10pt">Yang bertanda tangan dibawah ini saya</p>
<table class="legacy-table compact">
    <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr>
    <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr>
    <tr><td>3.</td><td>Tanggal Lahir</td><td>:</td><td class="write">{{ $death['reporter']['birth_place_date'] ?? '' }}</td></tr>
    <tr><td>4.</td><td>Umur</td><td>:</td><td class="write">{{ $death['reporter']['age'] ?? '' }} Tahun</td></tr>
    <tr><td>5.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['reporter']['occupation'] ?? '' }}</td></tr>
    <tr><td>6.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr>
</table>
<table class="legacy-table" style="margin-top:3pt"><tr><td class="label">Hubungan dengan Yang Meninggal</td><td class="colon">:</td><td class="write"></td></tr><tr><td>Dengan ini melaporkan Kematian</td><td>:</td><td></td></tr></table>
<table class="legacy-table compact" style="margin-top:9pt">
    <tr><td style="width:18pt">1.</td><td class="label">Nama Lengkap</td><td class="colon">:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr>
    <tr><td>2.</td><td>NIK</td><td>:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr>
    <tr><td>3.</td><td>No KK</td><td>:</td><td class="write">{{ $death['family']['kk_number'] ?? '' }}</td></tr>
    <tr><td>4.</td><td>Jenis Kelamin</td><td>:</td><td class="write">{{ $death['deceased']['gender'] ?? '' }}</td></tr>
    <tr><td>5.</td><td>Tempat Dilahirkan</td><td>:</td><td class="write">{{ $death['deceased']['birth_place_date'] ?? '' }}</td></tr>
    <tr><td>6.</td><td>Agama</td><td>:</td><td class="write">{{ $death['deceased']['religion'] ?? '' }}</td></tr>
    <tr><td>7.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['deceased']['occupation'] ?? '' }}</td></tr>
    <tr><td>8.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['deceased']['address'] ?? '' }}</td></tr>
    <tr><td>9.</td><td>Anak ke (dengan huruf)</td><td>:</td><td class="write">{{ $death['deceased']['birth_order'] ?? '' }}</td></tr>
    <tr><td>10.</td><td>Meninggal Hari/Tanggal</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }}</td></tr>
    <tr><td>11.</td><td>Jam</td><td>:</td><td class="write">{{ $death['deceased']['death_time'] ?? '' }} WIB</td></tr>
    <tr><td>12.</td><td>Sebab</td><td>:</td><td class="write">{{ $death['deceased']['cause'] ?? '' }}</td></tr>
    <tr><td>13.</td><td>Rincian Sebab</td><td>:</td><td class="write"></td></tr>
    <tr><td>14.</td><td>Yang Menerangkan</td><td>:</td><td class="write">{{ $death['deceased']['relation'] ?? '' }}</td></tr>
    <tr><td>15.</td><td>Tempat Meninggal</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }}</td></tr>
    <tr><td>16.</td><td>Kab/Kota Meninggal</td><td>:</td><td class="write">{{ $death['deceased']['death_city'] ?? '' }}</td></tr>
</table>
<p style="margin-top:5pt;text-align:justify">Demikian laporan saya ini saya buat dengan sebenar-benarnya dan disertai bukti dan dokumen pendukung pelaporan Kematian, apabila laporan saya ini dikemudian hari diketahui tidak benar maka saya siap dituntut sesuai peraturan perundang-undangan yang berlaku.</p>
<table class="signature-lines" style="margin-top:6pt"><tr><td>Mengetahui<br>Dukuh<br><div class="space"></div><strong><u></u></strong></td><td>{{ $signature['city'] ?? 'Bimomartani' }} &nbsp;&nbsp; {{ $signature['date'] ?? '' }}<br>Pelapor<br><div class="space"></div><strong><u>{{ $death['reporter']['name'] ?? '' }}</u></strong></td></tr></table>
@endsection
