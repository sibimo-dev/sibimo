@push('styles')
    .hr table.rows { width: 100%; margin-left: 32pt; }
    .hr td.lbl { width: 150pt; }
@endpush

@php
    $pp = 'letters.marriage-women.partials.';
    $m = $marriage;
    $b = $m['bride'];
    $h = $m['health'];
    $valid = collect([$h['valid_from'], $h['valid_until']])->filter()->implode(' - ');
@endphp

<div class="hr">
    @include($pp.'kop')

    <div class="judul u">SURAT KETERANGAN</div>
    <p>Menerangkan bahwa orang tersebut dibawah ini:</p>

    @include($pp.'rows', ['bold' => false, 'rows' => [
        'Nama'             => $b['name'],
        'Jenis Kelamin'    => $b['gender'],
        'Tempat/Tgl Lahir' => $b['birth'],
        'Nik'              => $b['nik'],
        'Kewarganegaraan'  => $b['citizenship'],
        'Agama'            => $b['religion'],
        'Pendidikan'       => $b['education'],
        'Pekerjaan'        => $b['occupation'],
        'Status'           => $b['status'],
        'Alamat'           => $b['address'],
        'Kelakuan'         => $h['conduct'],
        'Tujuan ke'        => $h['destination'],
    ]])

    @include($pp.'rows', ['marker' => 'none', 'bold' => false, 'rows' => [
        'Keperluan'            => $h['need'],
        'Keterangan lain-lain' => $h['note'],
    ]])

    <p style="margin-top:8pt">Surat pengantar ini berlaku tanggal : {{ $valid }}</p>
    <p style="margin-top:8pt">Demikian pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>

    @include($pp.'signature', ['leftTitle' => 'Pemegang', 'leftName' => $b['name']])
</div>