@extends('letters.layouts.form')

@section('title', 'Formulir Permohonan Pindah Datang WNI')

@php($kodeForm = 'F.1-31')

@section('content')
   @include('letters.region-grid', ['showHamlet' => true])

    <div class="judul-form">Formulir Permohonan Pindah Datang WNI</div>
    <div class="subjudul-form">Antar Kabupaten/Kota Atau Antar Provinsi</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:140pt;">Nomor Kartu keluarga</td><td class="f-box">{{ $form['origin']['kk_number'] ?? '' }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $form['origin']['head_of_family'] ?? '' }}</td></tr>
        <tr><td class="f-no">3.</td><td>Alamat</td><td class="f-box">{{ $form['origin']['address'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; text-align:right; font-size:9.5pt; padding-right:6pt;">RT/RW</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $form['origin']['rt'] ?? '' }}</td>
            <td style="width:6pt;"></td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $form['origin']['rw'] ?? '' }}</td>
            <td></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt;">{{ $form['origin']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kabupaten</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt;">{{ $form['origin']['district'] ?? '' }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:155pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt; width:80pt;">{{ $form['origin']['postal_code'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $form['origin']['phone'][$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no">4.</td><td style="width:140pt;">NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr>
            <td class="f-no">5.</td><td>Tempat/Tanggal Lahir</td>
            <td class="f-box">{{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}</td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap Pemohon</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA DAERAH TUJUAN</div>
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">1.</td>
            <td style="width:140pt; font-size:9.5pt;">Status KK Bagi Yang Pindah</td>
            <td style="border:1px solid #000; padding:3pt 4pt;">
                <span class="opsi-box">{{ $form['destination_kk_status_code'] ?? '' }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>
    <table class="frow">
        <tr><td class="f-no">2.</td><td style="width:140pt;">Nomor Kartu keluarga</td><td class="f-box">{{ $form['destination']['kk_number'] ?? '' }}</td></tr>
        <tr><td class="f-no">3.</td><td>NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="f-no">4.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $form['destination']['head_of_family'] ?? '' }}</td></tr>
        <tr><td class="f-no">5.</td><td>Tanggal Kedatangan</td><td class="f-box">{{ $form['arrival_date'] ?? '' }}</td></tr>
        <tr><td class="f-no">6.</td><td>Alamat yang Dituju</td><td class="f-box">{{ $form['destination']['address'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; text-align:right; font-size:9.5pt; padding-right:6pt;">RT</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $form['destination']['rt'] ?? '' }}</td>
            <td style="width:6pt; text-align:right; font-size:9.5pt;">RW</td>
            <td style="border:1px solid #000; width:30pt; text-align:center;">{{ $form['destination']['rw'] ?? '' }}</td>
            <td></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:155pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt;">{{ $form['destination']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kabupaten</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt;">{{ $form['destination']['district'] ?? '' }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:155pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:15pt; width:80pt;">{{ $form['destination']['postal_code'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">Telepon</td>
            <td>
                <table class="digit-box-full"><tr>
                    @for ($i = 0; $i < 15; $i++)
                        <td>{{ $form['destination']['phone'][$i] ?? '' }}</td>
                    @endfor
                </tr></table>
            </td>
        </tr>
    </table>

    <div class="sub-heading">6. Keluarga Yang Datang</div>
    <table class="tbl-bordered">
        <tr>
            <th style="width:20pt;">No</th><th>NIK</th><th>Nama</th><th style="width:70pt;">Masa Berlaku KTP</th><th style="width:60pt;">SHDK</th>
        </tr>
        @forelse (($form['family_members'] ?? []) as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td><td>{{ $row['nik'] ?? '' }}</td><td>{{ $row['name'] ?? '' }}</td>
                <td>{{ $row['valid_until'] ?? '' }}</td><td>{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @empty
            @for ($i = 0; $i < 6; $i++)
                <tr><td>{{ $i + 1 }}</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        @endforelse
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:14pt;">
        <tr>
            <td style="width:34%;"></td>
            <td style="width:33%;"></td>
            <td style="width:33%; text-align:right; font-size:9.5pt;">{{ $signature['city'] }} &nbsp;&nbsp; {{ $signature['date'] }}</td>
        </tr>
    </table>
    <table class="ttd-multi">
        <tr>
            <td width="34%">Petugas Registrasi</td>
            <td width="33%">Mengetahui<br>a.n LURAH BIMOMARTANI</td>
            <td width="33%">Pemohon</td>
        </tr>
        <tr>
            <td><div class="ttd-space"></div>{{ $form['registration_officer'] ?? '' }}</td>
            <td><div class="ttd-space"></div>{{ $signature['name'] }}</td>
            <td><div class="ttd-space"></div>{{ $applicant['name'] ?? '' }}</td>
        </tr>
    </table>

    <p style="margin-top:14pt; font-size:8.5pt;">
        Keterangan :<br>
        *) Diisi Oleh Petugas<br>
        - Formulir ini diisi di Desa
    </p>
@endsection