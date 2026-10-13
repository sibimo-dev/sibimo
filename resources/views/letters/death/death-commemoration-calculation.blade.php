@extends('letters.death.layout')

@section('title', 'Perhitungan Selamatan')
@section('content')
<div class="legacy-head"><div class="main">PERHITUNGAN SELAMATAN 3 HARI SAMPAI 1000 HARI</div></div>
<table class="legacy-table" style="width:72%;margin:17pt auto 0"><tr><td class="label">Nama</td><td class="colon">:</td><td class="write">{{ $death['deceased']['name'] ?? '' }}</td></tr><tr><td>Alamat</td><td>:</td><td class="write">{{ $death['deceased']['address'] ?? '' }}</td></tr></table>
<table class="grid" style="width:72%;margin:17pt auto 0"><tr><th colspan="2">Hari Meninggal</th></tr><tr><th>{{ $death['deceased']['death_date'] ?? '' }}</th><th></th></tr><tr><th>Jam</th><th>: &nbsp; {{ $death['deceased']['death_time'] ?? '' }}</th></tr></table>
<div style="width:72%;margin:4pt auto 0;font-weight:bold">Hari Meninggal (Jawa)</div>
<table class="grid" style="width:72%;margin:3pt auto 0"><tr><th>{{ $death['deceased']['death_date'] ?? '' }}</th><th></th></tr></table>
<table class="grid" style="width:72%;margin:17pt auto 0"><tr><th>Selamatan</th><th>Tanggal</th><th>Hari</th><th>Pasaran</th></tr>
@foreach ([['key'=>'3','label'=>'Selamatan 3 Hari'],['key'=>'7','label'=>'Selamatan 7 Hari'],['key'=>'40','label'=>'Selamatan 40 Hari'],['key'=>'100','label'=>'Selamatan 100 Hari'],['key'=>'365','label'=>'Selamatan Pendhak Pisan (1 Tahun)'],['key'=>'730','label'=>'Selamatan Pendhak Pindho (2 Tahun)'],['key'=>'1000','label'=>'Selamatan Nyewu Dina (1000 Hari)']] as $item)
<tr><td>{{ $item['label'] }}</td><td>{{ $death['selamatan'][$item['key']] ?? '' }}</td><td></td><td></td></tr>
@endforeach
</table>
<div class="right" style="margin-top:16pt">{{ $signature['city'] ?? 'Bimomartani' }} &nbsp; {{ $signature['date'] ?? '' }}</div>
@endsection
