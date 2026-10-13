@extends('letters.death.layout')

@section('title', 'Formulir Pelaporan Kematian')
@section('content')
<div class="legacy-head">
    <div class="main">FORMULIR PELAPORAN KEMATIAN</div>
    <div class="sub">(Untuk Mendapatkan Akta Kematian)</div>
    <div class="main">PEMERINTAH KABUPATEN SLEMAN</div>
</div>
<table class="legacy-table compact" style="margin-top:10pt">
    <tr><td style="width:18pt">1.</td><td class="label">No. KK</td><td class="colon">:</td><td class="write">{{ $death['family']['kk_number'] ?? '' }}</td></tr>
    <tr><td>2.</td><td class="label">Nama Kepala Keluarga</td><td>:</td><td class="write">{{ $death['family']['head_name'] ?? '' }}</td></tr>
</table>
<div class="legacy-section">
    <div class="legacy-section-title">JENAZAH</div>
    <table class="legacy-table compact">
        <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr>
        <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr>
        <tr><td>3.</td><td>Jenis Kelamin</td><td>:</td><td class="write">{{ $death['deceased']['gender'] ?? '' }}</td></tr>
        <tr><td>4.</td><td>Tempat Kelahiran</td><td>:</td><td class="write">{{ $death['deceased']['birth_place'] ?? '' }}</td></tr>
        <tr><td>5.</td><td>Tanggal lahir/Umur</td><td>:</td><td class="write">{{ $death['deceased']['birth_date'] ?? '' }} &nbsp;&nbsp;&nbsp; {{ $death['deceased']['age'] ?? '' }} Tahun</td></tr>
        <tr><td>6.</td><td>Agama</td><td>:</td><td class="write">{{ $death['deceased']['religion'] ?? '' }}</td></tr>
        <tr><td>7.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['deceased']['occupation'] ?? '' }}</td></tr>
        <tr><td>8.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['deceased']['address'] ?? '' }}</td></tr>
        <tr><td>9.</td><td>Anak ke (dengan huruf)</td><td>:</td><td class="write">{{ $death['deceased']['birth_order'] ?? '' }}</td></tr>
        <tr><td>10.</td><td>Meninggal Hari/Tanggal</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }}</td></tr>
        <tr><td>11.</td><td>Jam</td><td>:</td><td class="write">{{ $death['deceased']['death_time'] ?? '' }} WIB</td></tr>
        <tr><td>12.</td><td>Sebab</td><td>:</td><td class="write">{{ $death['deceased']['cause'] ?? '' }}</td></tr>
        <tr><td>13.</td><td>Tempat Kematian</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }}</td></tr>
        <tr><td>14.</td><td>Yang Menerangkan</td><td>:</td><td class="write">{{ $death['deceased']['relation'] ?? '' }}</td></tr>
    </table>
</div>
@foreach ([['key' => 'mother', 'title' => 'IBU'], ['key' => 'father', 'title' => 'AYAH']] as $parent)
<div class="legacy-section">
    <div class="legacy-section-title">{{ $parent['title'] }}</div>
    <table class="legacy-table compact">
        <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death[$parent['key']]['nik'] ?? '' }}</td></tr>
        <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death[$parent['key']]['name'] ?? '' }}</td></tr>
        <tr><td>3.</td><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write">{{ $death[$parent['key']]['birth_place_date'] ?? '' }}</td></tr>
        <tr><td>4.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death[$parent['key']]['occupation'] ?? '' }}</td></tr>
        <tr><td>5.</td><td>Alamat</td><td>:</td><td class="write">{{ $death[$parent['key']]['address'] ?? '' }}</td></tr>
        <tr><td>6.</td><td>Kewarganegaraan</td><td>:</td><td class="write"></td></tr>
    </table>
</div>
@endforeach
<div class="legacy-section">
    <div class="legacy-section-title">PELAPOR</div>
    <table class="legacy-table compact">
        <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr>
        <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr>
        <tr><td>3.</td><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write">{{ $death['reporter']['birth_place_date'] ?? '' }}</td></tr>
        <tr><td>4.</td><td>Umur</td><td>:</td><td class="write">{{ $death['reporter']['age'] ?? '' }} Tahun</td></tr>
        <tr><td>5.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['reporter']['occupation'] ?? '' }}</td></tr>
        <tr><td>6.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr>
        <tr><td>7.</td><td>Tanggal Lapor</td><td>:</td><td class="write">{{ $death['reporter']['report_date'] ?? '' }}</td></tr>
    </table>
</div>
<div class="legacy-section">
    <table class="two-col compact"><tr>
        @foreach ([0, 1] as $i)
        <td><div class="legacy-section-title">SAKSI {{ $i ? 'II' : 'I' }}</div><table class="legacy-table"><tr><td style="width:18pt">1.</td><td>NIK</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Umur</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['age'] ?? '' }} Tahun</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['address'] ?? '' }}</td></tr></table></td>
        @endforeach
    </tr></table>
</div>
<table class="legacy-table" style="margin-top:2pt"><tr><td class="form-label" style="width:170pt">DATA ADMINISTRASI <span class="small">(di isi oleh petugas)</span></td><td>1. Jenis Pelaporan &nbsp;&nbsp;: ____________________<br>2. Nama Petugas Register Desa &nbsp;: ____________________</td><td style="border:1px solid #000; height:30pt; width:120pt"><strong>Paraf Petugas</strong></td></tr></table>
<div class="small" style="margin-top:4pt">*) coret yang tidak perlu</div>
@endsection
