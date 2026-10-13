@extends('letters.death.layout')

@section('title', 'Pelaporan Pencatatan Kematian')
@section('content')
<div class="legacy-head"><div class="main">PELAPORAN PENCATATAN KEMATIAN</div></div>
<table class="legacy-table compact" style="margin-top:8pt"><tr><td class="label">Nomor Kartu Keluarga</td><td class="colon">:</td><td class="write">{{ $death['family']['kk_number'] ?? '' }}</td></tr><tr><td>Nama Kepala Keluarga</td><td>:</td><td class="write">{{ $death['family']['head_name'] ?? '' }}</td></tr></table>
<div class="legacy-section-title" style="margin-top:6pt">JENAZAH</div>
<table class="legacy-table compact">
    <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr>
    <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr>
    <tr><td>3.</td><td>Jenis Kelamin/Anak Ke</td><td>:</td><td class="write">{{ $death['deceased']['gender'] ?? '' }} &nbsp;&nbsp;&nbsp; Anak ke: {{ $death['deceased']['birth_order'] ?? '' }}</td></tr>
    <tr><td>4.</td><td>Tempat dan Tanggal Lahir/Umur</td><td>:</td><td class="write">{{ $death['deceased']['birth_place_date'] ?? '' }} &nbsp;&nbsp; Umur: {{ $death['deceased']['age'] ?? '' }}</td></tr>
    <tr><td>5.</td><td>Agama</td><td>:</td><td class="write">{{ $death['deceased']['religion'] ?? '' }}</td></tr>
    <tr><td>6.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['deceased']['occupation'] ?? '' }}</td></tr>
    <tr><td>7.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['deceased']['address'] ?? '' }}</td></tr>
    <tr><td>8.</td><td>Tanggal Dan Jam Kematian</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }} &nbsp;&nbsp; Jam: {{ $death['deceased']['death_time'] ?? '' }} WIB</td></tr>
    <tr><td>9.</td><td>Sebab</td><td>:</td><td class="write">{{ $death['deceased']['cause'] ?? '' }}</td></tr>
    <tr><td>10.</td><td>Tempat Kematian</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }}</td></tr>
    <tr><td>11.</td><td>Yang Menerangkan</td><td>:</td><td class="write">{{ $death['deceased']['relation'] ?? '' }}</td></tr>
</table>
@foreach ([['key' => 'mother', 'title' => 'IBU'], ['key' => 'father', 'title' => 'AYAH']] as $parent)
<div class="legacy-section-title" style="margin-top:6pt">{{ $parent['title'] }}</div><table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death[$parent['key']]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death[$parent['key']]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tempat Dan Tanggal Lahir/Umur</td><td>:</td><td class="write">{{ $death[$parent['key']]['birth_place_date'] ?? '' }}</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death[$parent['key']]['address'] ?? '' }} &nbsp;&nbsp; RT: ______ RW: ______</td></tr></table>
@endforeach
<div class="legacy-section-title" style="margin-top:6pt">PELAPOR</div><table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tempat Tanggal lahir</td><td>:</td><td class="write">{{ $death['reporter']['birth_place_date'] ?? '' }} &nbsp;&nbsp; Umur: {{ $death['reporter']['age'] ?? '' }}</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr></table>
<div class="legacy-section-title" style="margin-top:6pt">SAKSI I</div><table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td>:</td><td class="write">{{ $death['witnesses'][0]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['witnesses'][0]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tanggal Lahir/Umur</td><td>:</td><td class="write">{{ $death['witnesses'][0]['birth_place_date'] ?? '' }} &nbsp;&nbsp; {{ $death['witnesses'][0]['age'] ?? '' }} Tahun</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['witnesses'][0]['address'] ?? '' }}</td></tr></table>
<div class="legacy-section-title" style="margin-top:6pt">SAKSI II</div><table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td>:</td><td class="write">{{ $death['witnesses'][1]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['witnesses'][1]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tanggal Lahir/Umur</td><td>:</td><td class="write">{{ $death['witnesses'][1]['birth_place_date'] ?? '' }} &nbsp;&nbsp; {{ $death['witnesses'][1]['age'] ?? '' }} Tahun</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['witnesses'][1]['address'] ?? '' }}</td></tr></table>
<div class="right" style="margin-top:8pt">{{ $signature['city'] ?? 'Sleman' }} &nbsp;&nbsp; {{ $signature['date'] ?? '' }}<br>Pelapor<div style="height:32pt"></div><strong><u>{{ $death['reporter']['name'] ?? '' }}</u></strong></div>
@endsection
