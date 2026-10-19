@extends('letters.layouts.form')

@section('title', 'Surat Pengantar Permohonan Pindah WNI')

@php($kodeForm = 'F.1-25')

@section('content')
    @include('letters.region-grid', ['showHamlet' => true])


    <div class="judul-form">Surat Pengantar Permohonan Pindah WNI</div>
    <div class="subjudul-form">{{ $form['relocation_type_label'] ?? 'Antar Desa Dalam Satu Kecamatan' }}</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:135pt;">Nomor Kartu Keluarga</td><td class="f-box">{{ $form['origin']['kk_number'] ?? '' }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $form['origin']['head_of_family'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">3.</td>
            <td style="width:135pt; font-size:9.5pt;">Alamat</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['origin']['address'] ?? '' }}</td>
            <td style="width:26pt; font-size:9.5pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['origin']['rt'] ?? '' }}</td>
            <td style="width:28pt; font-size:9.5pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['origin']['rw'] ?? '' }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $form['origin']['hamlet'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['origin']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['origin']['district'] ?? '' }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt; width:80pt;">{{ $form['origin']['postal_code'] ?? '' }}</td>
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
        <tr><td class="f-no">4.</td><td style="width:135pt;">NIK Pemohon</td><td class="f-box">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr>
            <td class="f-no">5.</td><td>Tempat/Tanggal Lahir</td>
            <td class="f-box">{{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}</td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA KEPINDAHAN</div>

    {{-- 1. Alasan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt; vertical-align:top;">1.</td>
            <td style="width:135pt; font-size:9.5pt; vertical-align:top;">Alasan Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['relocation_reason_code'] ?? '' }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">1. Pekerjaan</td>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">3. Keamanan</td>
                        <td style="width:23%; font-size:9.5pt; padding:1pt 0;">5. Perumahan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">7. Lainnya (sebutkan)</td>
                    </tr>
                    <tr>
                        <td style="font-size:9.5pt; padding:1pt 0;">2. Pendidikan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">4. Kesehatan</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">6. Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">{{ $form['relocation_reason_other'] ?? '.....................' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 2. Alamat Tujuan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">2.</td>
            <td style="width:135pt; font-size:9.5pt;">Alamat Tujuan Pindah</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['destination']['address'] ?? '' }}</td>
            <td style="width:26pt; font-size:9.5pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['destination']['rt'] ?? '' }}</td>
            <td style="width:28pt; font-size:9.5pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['destination']['rw'] ?? '' }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $form['destination']['hamlet'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['destination']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9.5pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9.5pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt;">{{ $form['destination']['district'] ?? '' }}</td>
            <td style="font-size:9.5pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9.5pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:12pt; width:80pt;">{{ $form['destination']['postal_code'] ?? '' }}</td>
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

    {{-- 3. Jenis Kepindahan --}}
    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt; vertical-align:top;">3.</td>
            <td style="width:135pt; font-size:9.5pt; vertical-align:top;">Jenis Kepindahan</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['relocation_type_code'] ?? '' }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:50%; font-size:9.5pt; padding:1pt 0;">1. Kep. Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">3. Kep.Keluarga dan sbg Angg.Keluarga</td>
                    </tr>
                    <tr>
                        <td style="font-size:9.5pt; padding:1pt 0;">2. Kep.Keluarga dan seluruh Angg.Keluarga</td>
                        <td style="font-size:9.5pt; padding:1pt 0;">4. Anggota Keluarga</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:1.5pt;">
        <tr>
            <td style="width:15pt; font-size:9.5pt;">4.</td>
            <td style="width:135pt; font-size:9.5pt;">Status KK bagi yang tidak Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['origin_kk_status_code'] ?? '' }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
        <tr>
            <td style="font-size:9.5pt;">5.</td>
            <td style="font-size:9.5pt;">Status Nomor KK bagi yang Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['destination_kk_status_code'] ?? '' }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>

    <div class="sub-heading" style="margin-top:2pt;">6. Keluarga Yang Pindah</div>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9pt; margin-top:4pt; page-break-inside: avoid;">
        <tr>
            <th style="border:1px solid #000; width:18pt; padding:1pt 2pt; line-height:1.1;">NO</th>
            <th style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">NIK</th>
            <th style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">NAMA</th>
            <th style="border:1px solid #000; width:60pt; padding:1pt 2pt; line-height:1.1;">MASA BERLAKU KTP S/D</th>
            <th style="border:1px solid #000; width:55pt; padding:1pt 2pt; line-height:1.1;">SHDK</th>
        </tr>
        @forelse (($form['family_members'] ?? []) as $i => $row)
            <tr>
                <td style="border:1px solid #000; padding:1pt 2pt; height:14pt; line-height:1.1;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['nik'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['name'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['valid_until'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;">{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @empty
            @for ($i = 0; $i < 7; $i++)
                <tr>
                    <td style="border:1px solid #000; padding:1pt 2pt; height:14pt; line-height:1.1;">{{ $i + 1 }}</td>
                    <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;"></td>
                    <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;"></td>
                    <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;"></td>
                    <td style="border:1px solid #000; padding:1pt 2pt; line-height:1.1;"></td>
                </tr>
            @endfor
        @endforelse
    </table>

    <div style="page-break-inside: avoid;">
        <table style="width:100%; border-collapse:collapse; margin-top:4pt;">
            <tr>
                <td style="width:34%;"></td>
                <td style="width:33%; text-align:center; font-size:9.5pt;">{{ $signature['city'] }}</td>
                <td style="width:33%; text-align:right; font-size:9.5pt;">{{ $signature['date'] }}</td>
            </tr>
        </table>
        <table class="ttd-multi" style="page-break-inside: avoid;">
            <tr>
                <td width="34%">Petugas Registrasi,</td>
                <td width="33%">a.n LURAH BIMOMARTANI</td>
                <td width="33%">Pemohon,</td>
            </tr>
            <tr style="page-break-inside: avoid;">
                <td><div class="ttd-space"></div>{{ $form['registration_officer'] ?? '' }}</td>
                <td><div class="ttd-space"></div>{{ $signature['name'] }}</td>
                <td><div class="ttd-space"></div>{{ $applicant['name'] ?? '' }}</td>
            </tr>
        </table>
    </div>
@endsection