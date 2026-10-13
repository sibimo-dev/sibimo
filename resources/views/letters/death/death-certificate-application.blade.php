@extends('letters.death.layout')

@section('title', 'Permohonan Akta Kematian')
@section('content')
<table style="width:100%"><tr><td><div class="title" style="margin-top:0">Permohonan Akta Kematian</div></td><td style="width:115pt;border:1px solid #000;height:28pt;vertical-align:top;padding:3pt"><strong>NO.</strong> {{ $number ?? '' }}</td></tr></table>
<div class="section-title">A. &nbsp; DATA KELUARGA</div>
<table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">Nomor Kartu Keluarga</td><td class="colon">:</td><td class="write">{{ $death['family']['kk_number'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Kepala Keluarga</td><td>:</td><td class="write">{{ $death['family']['head_name'] ?? '' }}</td></tr></table>
<div class="section-title">B. &nbsp; DATA JENAZAH</div>
<table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['deceased']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Tempat Dan Tgl Lahir</td><td>:</td><td class="write">{{ $death['deceased']['birth_place_date'] ?? '' }}</td></tr><tr><td>4.</td><td>Tempat Dan Tgl Kematian</td><td>:</td><td class="write">{{ $death['deceased']['death_city'] ?? '' }} / {{ $death['deceased']['death_date'] ?? '' }}</td></tr><tr><td>5.</td><td>Jenis Kelamin</td><td>:</td><td class="write">{{ $death['deceased']['gender'] ?? '' }}</td></tr></table>
<div class="section-title">C. &nbsp; DATA IBU KANDUNG</div>
<table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['mother']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['mother']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['mother']['address'] ?? '' }}<br>RT: __________ &nbsp;&nbsp;&nbsp;&nbsp; RW: __________</td></tr></table>
<div class="section-title">D. &nbsp; DATA AYAH KANDUNG</div>
<table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['father']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['father']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['father']['address'] ?? '' }}<br>RT: __________ &nbsp;&nbsp;&nbsp;&nbsp; RW: __________</td></tr></table>
<div class="section-title">E. &nbsp; DATA PELAPOR</div>
<table class="legacy-table compact"><tr><td style="width:18pt">1.</td><td class="label">NIK</td><td class="colon">:</td><td class="write">{{ $death['reporter']['nik'] ?? '' }}</td></tr><tr><td>2.</td><td>Nama Lengkap</td><td>:</td><td class="write">{{ $death['reporter']['name'] ?? '' }}</td></tr><tr><td>3.</td><td>Alamat</td><td>:</td><td class="write">{{ $death['reporter']['address'] ?? '' }}</td></tr><tr><td>4.</td><td>Nomor HP/Email</td><td>:</td><td class="write"></td></tr><tr><td>5.</td><td>Tanggal Permohonan</td><td>:</td><td class="write">{{ $death['reporter']['report_date'] ?? '' }}</td></tr><tr><td>6.</td><td>Tanda Tangan Pemohon</td><td>:</td><td class="write"></td></tr></table>
<div class="section-title">F. &nbsp; DOKUMEN PERSYARATAN</div>
<table class="checklist">
@foreach(['Surat Keterangan Kematian dari dokter/paramedis','Surat Keterangan Kematian dari Kalurahan','Fotocopi Kartu Keluarga (KK) yang meninggal','Fotocopi Kartu Keluarga (KK) ahli waris','Fotocopi KTP-el yang meninggal','Fotocopi KTP-el ahli waris','Fotocopi KTP-el 2 (dua) orang saksi','Surat Kuasa dan Fotocopi KTP-el penerima kuasa','Fotocopi paspor bagi WNI bukan penduduk dan orang asing','Fotocopi Surat Keterangan Tempat Tinggal (SKKT) orang tua bagi pemegang ITAS'] as $document)
<tr><td class="check-box"></td><td>{{ $document }}</td></tr>
@endforeach
</table>
<div class="right" style="margin-top:4pt">Sleman, ____________________<br>Petugas Penerima Berkas,</div>
@endsection
