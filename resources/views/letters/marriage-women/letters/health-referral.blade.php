@php $pp = 'letters.marriage-women.partials.'; $m = $marriage; $b = $m['bride']; $h = $m['health']; @endphp
@include($pp.'kop')

<div class="judul">SURAT KETERANGAN</div>
<p>Menerangkan bahwa orang tersebut dibawah ini:</p>
@include($pp.'rows', ['bold' => false, 'rows' => [
    'Nama'              => $b['name'],
    'Jenis Kelamin'     => $b['gender'],
    'Tempat/ Tgl Lahir' => $b['birth'],
    'Nik'               => $b['nik'],
    'Kewarganegaraan'   => $b['citizenship'],
    'Agama'             => $b['religion'],
    'Pendidikan'        => $b['education'],
    'Pekerjaan'         => $b['occupation'],
    'Status'            => $b['status'],
    'Alamat'            => $b['address'],
    'Kelakuan'          => $h['conduct'],
    'Tujuan ke'         => $h['destination'],
]])
@include($pp.'rows', ['marker' => 'none', 'bold' => false, 'rows' => [
    'Keperluan'                            => $h['need'],
    'Keterangan lain-lain'                 => $h['note'],
    'Surat pengantar ini berlaku tanggal'  => collect([$h['valid_from'], $h['valid_until']])->filter()->implode(' - '),]])

<p>Demikian pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
@include($pp.'signature', ['leftTitle' => 'Pemegang', 'leftName' => $b['name']])