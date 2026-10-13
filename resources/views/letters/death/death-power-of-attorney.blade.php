@extends('letters.death.layout')

@section('title', 'Surat Kuasa')
@section('content')
<div class="title">Surat Kuasa</div>
<p style="margin-top:12pt">Yang bertanda tangan dibawah ini:</p>
<table class="legacy-table" style="margin-left:65pt;width:80%"><tr><td class="label">Nama</td><td>:</td><td class="write">{{ $death['attorney']['grantor']['name'] ?? '' }}</td></tr><tr><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['attorney']['grantor']['occupation'] ?? '' }}</td></tr><tr><td>Alamat</td><td>:</td><td class="write">{{ $death['attorney']['grantor']['address'] ?? '' }}</td></tr></table>
<p style="margin-top:10pt">Dengan ini memberi kuasa kepada</p>
<table class="legacy-table" style="margin-left:65pt;width:80%"><tr><td class="label">Nama</td><td>:</td><td class="write">{{ $death['attorney']['attorney']['name'] ?? '' }}</td></tr><tr><td>Pekerjaan</td><td>:</td><td class="write">{{ $death['attorney']['attorney']['occupation'] ?? '' }}</td></tr><tr><td>Alamat</td><td>:</td><td class="write">{{ $death['attorney']['attorney']['address'] ?? '' }}</td></tr></table>
<div class="title" style="margin-top:13pt">Khusus</div>
<p style="margin-top:10pt;text-align:justify">Untuk mewakili diri saya mengajukan permohonan Akta Kematian ke Dinas Kependudukan dan Pencatatan Sipil Kabupaten Sleman untuk:</p>
<div style="text-align:center;border-bottom:1px dotted #000;height:22pt;margin:4pt 35pt">{{ $death['deceased']['name'] ?? '' }}</div>
<p style="text-align:justify;margin-top:8pt">berhubung saya tidak dapat menghadap sendiri. Pemegang kuasa ini diberi hak untuk menghadap dan berbicara dimuka Dinas Kependudukan dan Pencatatan Sipil Kabupaten Sleman segala sesuatu yang kesemuanya untuk dan demi Akta Kematian tersebut sepanjang yang berhubungan perkara ini sesuai dengan Hukum Acara Perdata Tingkat pertama.</p>
<table class="signature-lines"><tr><td>Yang Diberi Kuasa<div class="space"></div><strong><u>{{ $death['attorney']['attorney']['name'] ?? '' }}</u></strong></td><td>Yang Memberi Kuasa<div class="space"></div><strong><u>{{ $death['attorney']['grantor']['name'] ?? '' }}</u></strong></td></tr></table>
<table class="signature-lines" style="margin-top:13pt"><tr><td>Mengetahui<br><br>{{ $signature['prefix'][0] ?? 'a.n LURAH BIMOMARTANI' }}<br>{{ $signature['position'] ?? '' }}<div class="long-space"></div><strong><u>{{ $signature['name'] ?? '' }}</u></strong></td><td></td></tr></table>
@endsection
