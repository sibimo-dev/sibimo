@extends('letters.layouts.base')

@section('title', 'Surat Balasan Penelitian')

@section('extra_css')
    .info-line { margin-top: 6pt; }
    .tujuan { margin-top: 18pt; }
    .data-gap { margin-top: 18pt; }
@endsection

@section('content')
<p class="info-line">Nomor : {{ $number }}</p>
<p class="info-line">Hal : <i>Balasan Penelitian</i></p>

<div class="tujuan">
    <p>Kepada</p>
    <p>Yth : {{ $recipient }}</p>
    <p>Di Tempat</p>
</div>

<p class="gap-top-lg">
    Yang bertanda tangan dibawah ini Lurah Bimomartani, Kapanewon Ngemplak, Kabupaten Sleman,
    menindak lanjuti surat saudara Nomor : {{ $ref_number }}, Telah melaksanakan Penelitian
    di Kalurahan Bimomartani Atas Nama :
</p>

<table class="data data-gap">
    <tr>
        <td class="lbl">Nama Mahasiswa</td>
        <td class="sep">:</td>
        <td class="val">{{ $researcher['name'] }}</td>
    </tr>
    <tr>
        <td class="lbl">Nim</td>
        <td class="sep">:</td>
        <td class="val">{{ $researcher['nim'] }}</td>
    </tr>
    <tr>
        <td class="lbl">Program Studi</td>
        <td class="sep">:</td>
        <td class="val">{{ $researcher['study_program'] }}</td>
    </tr>
    <tr>
        <td class="lbl">Fakultas</td>
        <td class="sep">:</td>
        <td class="val">{{ $researcher['faculty'] }}</td>
    </tr>
</table>

<p class="gap-top-lg">Demikian kami sampaikan atas perhatianya kami haturkan terima kasih.</p>
@endsection