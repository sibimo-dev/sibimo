@extends('letters.layouts.form')

@section('title', 'Surat Kuasa Pelayanan Administrasi Kependudukan')

@section('extra_css')
    table.kuasa-data { width: 100%; border-collapse: collapse; margin-top: 4pt; }
    table.kuasa-data td { padding: 2pt 0; vertical-align: top; }
    td.kd-lbl { width: 160pt; }
    td.kd-sep { width: 10pt; }
    td.kd-val { border-bottom: 1px dashed #000; padding-left: 3pt; height: 12pt; }
    .kuasa-p { margin-top: 10pt; }

    table.kuasa-ttd { width: 100%; border-collapse: collapse; margin-top: 12pt; }
    table.kuasa-ttd td.kt-col { border: 1px solid #000; width: 50%; vertical-align: top; padding: 6pt; }
    .kt-label { text-align: center; }
    table.kt-inner { width: 100%; border-collapse: collapse; margin-top: 6pt; }
    table.kt-inner td { border: none; vertical-align: bottom; padding: 0; }
    td.kt-materai-cell { width: 75pt; }
    .kt-materai-box {
        border: 1px solid #000; width: 60pt; height: 40pt;
        text-align: center; font-size: 8pt;
    }
    .kt-materai-box span { display: block; padding-top: 12pt; }
    td.kt-name-cell { text-align: center; }
    .kt-space { height: 40pt; }
    .kuasa-note { margin-top: 6pt; font-size: 8.5pt; }
@endsection

@section('content')
<div class="judul-form">SURAT KUASA DALAM PELAYANAN</div>
<div class="judul-form" style="margin-top:0;">ADMINISTRASI KEPENDUDUKAN</div>

<p class="kuasa-p">
    Pada hari ini........... Tanggal...........Bulan...........Tahun...... bertempat di
</p>
<p>Bimomartani&nbsp;&nbsp;&nbsp;saya :</p>

<table class="kuasa-data">
    <tr>
        <td class="kd-lbl">Nama</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $applicant['name'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">NIK</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $applicant['nik'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Tempat Dan Tanggal Lahir</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $applicant['birth'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Pekerjaan</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $applicant['occupation'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Alamat</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $applicant['address'] }}</td>
    </tr>
</table>

<p class="kuasa-p">Dengan ini memberi kuasa kepada</p>
<table class="kuasa-data">
    <tr>
        <td class="kd-lbl">Nama</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $attorney['name'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">NIK</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $attorney['nik'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Tempat Dan Tanggal Lahir</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $attorney['birth'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Pekerjaan</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $attorney['occupation'] }}</td>
    </tr>
    <tr>
        <td class="kd-lbl">Alamat</td>
        <td class="kd-sep">:</td>
        <td class="kd-val">{{ $attorney['address'] }}</td>
    </tr>
</table>

<p class="kuasa-p">
    Untuk mengisi formulir dalam pelayanan administrasi kependudukan, sesuai keterangan dan
    kelengkapan persyaratan yang saya berikan seperti keadaan yang sebenarnya dikarenakan
    kondisi saya dalam keadaan...............
</p>

<table class="kuasa-ttd">
    <tr>
        <td class="kt-col">
            <div class="kt-label">Yang Diberi Kuasa</div>
            <div class="kt-space"></div>
        </td>
        <td class="kt-col">
            <div class="kt-label">Yang Memberi Kuasa</div>
            <table class="kt-inner">
                <tr>
                    <td class="kt-materai-cell">
                        <div class="kt-materai-box"><span>Materai<br>Cukup</span></div>
                    </td>
                    <td class="kt-name-cell">{{ $applicant['name'] }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p class="kuasa-note">*) Coret yang tidak sesuai</p>
@endsection