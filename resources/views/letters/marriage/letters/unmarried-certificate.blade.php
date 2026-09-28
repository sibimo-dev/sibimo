@push('styles')
    .uc table.rows { width: 100%; margin-left: 0; }
    .uc table.rows td.val { border-bottom: 1px dashed #000; }
    .uc td.lbl { width: 120pt; }
@endpush
@php $pp = 'letters.marriage.partials.'; $m = $marriage; $b = $m['bride']; $uc = $m['unmarried_certificate']; @endphp
<div class="uc">
@include($pp.'kop')

<div class="judul u" style="margin-top:18pt">SURAT KETERANGAN</div>
<div class="nomor">NOMOR : {{ $uc['letter_number'] }}</div>

<p style="margin-top:14pt">Yang bertanda tangan dibawah ini</p>
@include($pp.'rows', ['marker' => 'none', 'bold' => false, 'rows' => [
    'Nama'    => $signer['name'],
    'Jabatan' => null,
]])

<p style="margin-top:10pt">Dengan ini menerangkan bahwa</p>
@include($pp.'rows', ['marker' => 'none', 'bold' => false, 'rows' => [
    'Nama'              => $b['name'],
    'Tempat/Tgl. Lahir' => $b['birth'],
    'NIK'               => $b['nik'],
    'Jenis Kelamin'     => $b['gender'],
    'Agama'             => $b['religion'],
    'Pekerjaan'         => $b['occupation'],
    'Alamat'            => $b['address'],
]])

<p style="margin-top:10pt">berdasarkan data yang ada yang bersangkutan saat ini berstatus &nbsp;&nbsp;&nbsp;&nbsp; Belum Kawin /Belum Nikah</p>
<p style="margin-top:8pt">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>

@include($pp.'signature', ['dateLong' => $uc['date'], 'rightTitle' => $signature['position']])
</div>