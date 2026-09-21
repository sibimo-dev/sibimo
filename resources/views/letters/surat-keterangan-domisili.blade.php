{{--
    Surat Keterangan Domisili (perusahaan / yayasan), kop tanpa aksara Jawa.
    Isi kolom letter_types.blade_view dengan: letters.surat-keterangan-domisili

    Data yang dipakai (dibentuk oleh LetterPdfService::viewData):
      $nomor, $signer, $company[name, activity, building_status, building_use,
      person_in_charge, employee_count, phone, address], $applicant (= pemilik), $signature
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Domisili')
@section('hide_aksara', '1')
@section('date_long', '1')

@section('extra_css')
    table.data td { padding: 1.4pt 0; }
    table.signer { margin-left: 26pt; width: 360pt; }
    table.signer td.lbl { width: 75pt; }
    table.company { margin-left: 26pt; width: 453pt; }
    table.company td.lbl { width: 200pt; }
    table.owner { margin-left: 26pt; width: 453pt; }
    table.owner td.lbl { width: 115pt; }
    .kop-line { margin-top: 12pt; }
@endsection

@section('content')
    <div class="judul">SURAT KETERANGAN DOMISILI</div>
    <div class="nomor">NOMOR : {{ $nomor }}</div>

    <p class="gap-top-lg">Yang bertanda tangan dibawah ini</p>
    <table class="data signer">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $signer['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jabatan</td>
            <td class="sep">:</td>
            <td class="val">{{ $signer['position'] }}</td>
        </tr>
    </table>

    <p class="gap-top">Dengan ini menerangkan bahwa</p>
    <table class="data company" style="margin-top: 6pt;">
        <tr>
            <td class="lbl">Nama Perusahaan/Yayasan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Usaha/Kegiatan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['activity'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Status Bangunan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['building_status'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Kegunaan Bangunan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['building_use'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Nama Penanggung Jawab/Pimpinan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['person_in_charge'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jumlah Karyawan</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['employee_count'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Nomer Telepon/HP</td>
            <td class="sep">:</td>
            <td class="val">{{ $company['phone'] }}</td>
        </tr>
    </table>

    <p class="gap-top">Pada saat Surat Keterangan ini dibuat benar - benar berdomisili di Alamat</p>
    <p style="margin-top: 3pt;">{{ $company['address'] }}</p>

    <p class="gap-top">Dengan identitas Pemilik</p>
    <table class="data owner" style="margin-top: 4pt;">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat/Tgl. Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['nik'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['gender'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Status Perkawinan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['marital_status'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Agama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['religion'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
    </table>

    <p class="gap-top">Demikian surat keterangan ini dibuat untuk dipergunakan seperlunya.</p>
@endsection