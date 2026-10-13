@extends('letters.death.layout')

@section('title', 'Surat Keterangan Kematian')
@section('extra_css')
    @page { margin-top: .4cm; margin-bottom: .4cm; }
    body { font-size: 7.2pt; line-height: 1.04; }
    .kop-1 { font-size: 11.5pt; }
    .kop-2 { font-size: 13pt; line-height: 14pt; }
    .kop-3 { font-size: 15pt; line-height: 15pt; }
    .kop-logo img { width: 68pt; height: 90pt; }
    .aksara { height: 28pt; }
    .aksara img { width: 230pt; max-height: 28pt; }
    .kop-info { font-size: 8.5pt; }
    .kop-line { margin-top: 4pt; margin-bottom: 5pt; }
    .legacy-table td { padding-top: .3pt; padding-bottom: .3pt; }
    .legacy-table td { line-height: .88; }
    .legacy-table .write { height: 9pt; }
    .legacy-section { padding-top: 1pt; padding-bottom: 1pt; }
    .legacy-section-title { margin-bottom: 0; }
@endsection
@section('content')
<div class="title">Surat Keterangan Kematian</div>
<div class="number">NOMOR {{ $number ?? '' }}</div>
<table class="legacy-table compact" style="margin-bottom:4pt"><tr><td style="width:18pt">1.</td><td class="label">Nama Kepala Keluarga</td><td class="colon">:</td><td class="write">{{ $death['family']['head_name'] ?? '' }}</td></tr><tr><td>2.</td><td>Nomor Kartu Keluarga</td><td>:</td><td class="write">{{ $death['family']['kk_number'] ?? '' }}</td></tr></table>
<div class="legacy-section"><div class="legacy-section-title">JENAZAH</div><table class="legacy-table compact">
    <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr>
    <tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr>
    <tr><td>3.</td><td>Jenis Kelamin</td><td>:</td><td class="write">{{ $death['deceased']['gender'] ?? '' }}</td></tr>
    <tr><td>4.</td><td>Tempat Kelahiran</td><td>:</td><td class="write">{{ $death['deceased']['birth_place'] ?? '' }}</td></tr>
    <tr><td>5.</td><td>Tanggal Lahir/Umur</td><td>:</td><td class="write">{{ $death['deceased']['birth_date'] ?? '' }} / {{ $death['deceased']['age'] ?? '' }} Tahun</td></tr>
    <tr><td>6.</td><td>Agama</td><td>:</td><td class="write">{{ $death['deceased']['religion'] ?? '' }}</td></tr>
    <tr><td>7.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['deceased']['occupation'] ?? '' }}</td></tr>
    <tr><td>8.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['deceased']['address'] ?? '' }}</td></tr>
    <tr><td>9.</td><td>Anak ke (dengan huruf)</td><td>:</td><td class="write">{{ $death['deceased']['birth_order'] ?? '' }}</td></tr>
    <tr><td>10.</td><td>Meninggal Hari/Tanggal</td><td>:</td><td class="write">{{ $death['deceased']['death_date'] ?? '' }}</td></tr>
    <tr><td>11.</td><td>Tempat Meninggal</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }} / {{ $death['deceased']['death_city'] ?? '' }}</td></tr>
    <tr><td>12.</td><td>Jam</td><td>:</td><td class="write">{{ $death['deceased']['death_time'] ?? '' }} WIB</td></tr>
    <tr><td>13.</td><td>Sebab</td><td>:</td><td class="write">{{ $death['deceased']['cause'] ?? '' }}</td></tr>
    <tr><td>14.</td><td>Tempat Kematian</td><td>:</td><td class="write">{{ $death['deceased']['death_place'] ?? '' }}</td></tr>
    <tr><td>15.</td><td>Yang Menerangkan</td><td>:</td><td class="write">{{ $death['deceased']['relation'] ?? '' }}</td></tr>
</table></div>
@foreach ([['key' => 'mother', 'title' => 'IBU'], ['key' => 'father', 'title' => 'AYAH']] as $parent)
<div class="legacy-section"><div class="legacy-section-title">{{ $parent['title'] }}</div><table class="legacy-table compact">
    <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death[$parent['key']]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death[$parent['key']]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write">{{ $death[$parent['key']]['birth_place_date'] ?? '' }}</td></tr><tr><td>4.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death[$parent['key']]['occupation'] ?? '' }}</td></tr><tr><td>5.</td><td>Alamat (Sesuai KTP)</td><td>:</td><td class="write">{{ $death[$parent['key']]['address'] ?? '' }}</td></tr><tr><td>6.</td><td>Kewarganegaraan</td><td>:</td><td class="write"></td></tr>
</table></div>
@endforeach
<div class="legacy-section"><div class="legacy-section-title">PELAPOR</div><table class="legacy-table compact">
    <tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tempat/Tanggal Lahir</td><td>:</td><td class="write">{{ $death['reporter']['birth_place_date'] ?? '' }}</td></tr><tr><td>4.</td><td>Umur</td><td>:</td><td class="write">{{ $death['reporter']['age'] ?? '' }} Tahun</td></tr><tr><td>5.</td><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['reporter']['occupation'] ?? '' }}</td></tr><tr><td>6.</td><td>Alamat (Sesuai KTP)</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr><tr><td>7.</td><td>Tanggal Lapor</td><td>:</td><td class="write">{{ $death['reporter']['report_date'] ?? '' }}</td></tr><tr><td>8.</td><td>Tanda Tangan</td><td>:</td><td class="write"></td></tr>
</table></div>
<div class="legacy-section"><table class="two-col compact"><tr>@foreach ([0, 1] as $i)<td><div class="legacy-section-title">SAKSI {{ $i ? 'II' : '' }}</div><table class="legacy-table"><tr><td style="width:18pt">1.</td><td>NIK</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Umur</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['age'] ?? '' }} Tahun</td></tr><tr><td>4.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['witnesses'][$i]['address'] ?? '' }}</td></tr></table></td>@endforeach</tr></table></div>
<table class="signature-lines"><tr><td></td><td>{{ $signature['city'] ?? 'Bimomartani' }}, {{ $signature['date'] ?? '' }}<br>{{ $signature['prefix'][0] ?? '' }}<div class="space"></div><strong><u>{{ $signature['name'] ?? '' }}</u></strong></td></tr></table>
@endsection
