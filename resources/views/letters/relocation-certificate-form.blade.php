@extends('letters.layouts.form')

@section('title', 'Formulir Keterangan Pindah WNI')

@php($kodeForm = 'F.1-26')

@section('extra_css')
    table.kop-dukcapil { width: 100%; border-collapse: collapse; }
    table.kop-dukcapil td { padding: 0; vertical-align: middle; }
    table.kop-dukcapil td.kop-logo { width: 62pt; }
    table.kop-dukcapil td.kop-logo img { width: 52pt; height: 68pt; }
    table.kop-dukcapil td.kop-text { text-align: center; padding-right: 62pt; }
    .kopd-1 { font-size: 10.5pt; font-weight: bold; line-height: 13pt; }
    .kopd-2 { font-size: 12.5pt; font-weight: bold; line-height: 15pt; }
    .kopd-info { font-size: 8.5pt; line-height: 1.2; }
    .kopd-web { font-size: 8.5pt; font-weight: bold; line-height: 1.2; }
    .kop-line { border-bottom: 2pt solid #000; margin-top: 3pt; margin-bottom: 2pt; }
    table.frow { border-collapse: separate; border-spacing: 0 2pt; margin-top: 1pt; }
    table.frow td { font-size: 9pt; padding: 0; }
    td.f-box { padding: 1pt 4pt; height: 11pt; }
    table.wgrid { margin-top: 1pt; }
    table.wgrid td.wg-label { font-size: 9pt; }
    table.wgrid table.wg-kode td { height: 10pt; font-size: 8.5pt; }
    table.wgrid td.wg-val { padding: 1pt 4pt; }
    table.digit-box-full td { height: 11pt; }
    .opsi-box { height: 11pt; }
    .sub-heading { margin-top: 2pt; font-size: 9pt; }
    .judul-form { margin-top: 2pt; font-size: 10.5pt; }
    .ttd-left .ttd-space { height: 26pt; }
    table.ttd-left { width: 100%; border-collapse: collapse; margin-top: 2pt; }
    table.ttd-left td { font-size: 9.5pt; vertical-align: top; padding: 0; }
@endsection

@section('content')
    {{-- ===== KOP DUKCAPIL ===== --}}
    <table class="kop-dukcapil">
        <tr>
            <td class="kop-logo"><img src="{{ $logo }}" alt="Logo Kabupaten Sleman"></td>
            <td class="kop-text">
                <div class="kopd-1">PEMERINTAH KABUPATEN SLEMAN</div>
                <div class="kopd-2">DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</div>
                <div class="kopd-info">Jalan KRT Pringgodiningrat No. 3 Beran, Tridadi, Sleman , DIY, 55511</div>
                <div class="kopd-info">Telepon ( 0274 ) 868362, Faksimile ( 0274 ) 868945</div>
                <div class="kopd-web">website : www.capil.slemankab.go.id, E-mail : dukcapil@slemankab.go.id</div>
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>

    @include('letters.region-grid', ['showHamlet' => true])

    <div class="judul-form">Formulir Keterangan Pindah WNI</div>
    <div class="subjudul-form">{{ $form['relocation_type_label'] ?? 'Antar Desa Dalam Satu Kecamatan' }}</div>
    <div class="subjudul-form">No. {{ $number }}</div>

    <div class="sub-heading">DATA DAERAH ASAL</div>
    <table class="frow">
        <tr><td class="f-no">1.</td><td style="width:135pt;">Nomor Kartu Keluarga</td><td class="f-box">{{ $form['origin']['kk_number'] ?? '' }}</td></tr>
        <tr><td class="f-no">2.</td><td>Nama Kepala Keluarga</td><td class="f-box">{{ $form['origin']['head_of_family'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">3.</td>
            <td style="width:135pt; font-size:9pt;">Alamat</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['origin']['address'] ?? '' }}</td>
            <td style="width:26pt; font-size:9pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['origin']['rt'] ?? '' }}</td>
            <td style="width:28pt; font-size:9pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['origin']['rw'] ?? '' }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $form['origin']['hamlet'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['origin']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['origin']['district'] ?? '' }}</td>
            <td style="font-size:9pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['origin']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $form['origin']['postal_code'] ?? '' }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">Telepon</td>
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
            <td class="f-box">
                @if (!empty($applicant['birth_place']) || !empty($applicant['birth_date']))
                    {{ $applicant['birth_place'] ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $applicant['birth_date'] ?? '' }}
                @else
                    {{ $applicant['birth'] ?? '' }}
                @endif
            </td>
        </tr>
        <tr><td class="f-no">6.</td><td>Nama Lengkap</td><td class="f-box">{{ $applicant['name'] ?? '' }}</td></tr>
    </table>

    <div class="sub-heading">DATA KEPINDAHAN</div>

    {{-- 1. Alasan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt; vertical-align:top;">1.</td>
            <td style="width:135pt; font-size:9pt; vertical-align:top;">Alasan Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['relocation_reason_code'] ?? '' }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">1. Pekerjaan</td>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">3. Keamanan</td>
                        <td style="width:23%; font-size:9pt; padding:1pt 0;">5. Perumahan</td>
                        <td style="font-size:9pt; padding:1pt 0;">7. Lainnya (sebutkan)</td>
                    </tr>
                    <tr>
                        <td style="font-size:9pt; padding:1pt 0;">2. Pendidikan</td>
                        <td style="font-size:9pt; padding:1pt 0;">4. Kesehatan</td>
                        <td style="font-size:9pt; padding:1pt 0;">6. Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">{{ $form['relocation_reason_other'] ?? '.....................' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 2. Alamat Tujuan Pindah --}}
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">2.</td>
            <td style="width:135pt; font-size:9pt;">Alamat Tujuan Pindah</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['destination']['address'] ?? '' }}</td>
            <td style="width:26pt; font-size:9pt; text-align:center;">RT</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['destination']['rt'] ?? '' }}</td>
            <td style="width:28pt; font-size:9pt; text-align:center;">RW</td>
            <td style="border:1px solid #000; width:40pt; text-align:center;">{{ $form['destination']['rw'] ?? '' }}</td>
        </tr>
    </table>

    <table class="frow">
        <tr><td class="f-no"></td><td style="width:135pt;">Dusun / Dukuh / Kampung</td><td class="f-box">{{ $form['destination']['hamlet'] ?? '' }}</td></tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">a. Desa/Kelurahan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['destination']['village'] ?? '' }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">c. Kab/Kota</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['regency'] ?? '' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-size:9pt;">b. Kecamatan</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt;">{{ $form['destination']['district'] ?? '' }}</td>
            <td style="font-size:9pt; padding-left:6pt;">d. Provinsi</td>
            <td style="border:1px solid #000; padding-left:4pt;">{{ $form['destination']['province'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="width:15pt;"></td>
            <td style="width:135pt; font-size:9pt;">Kode Pos</td>
            <td style="border:1px solid #000; padding-left:4pt; height:11pt; width:80pt;">{{ $form['destination']['postal_code'] ?? '' }}</td>
            <td style="width:70pt; font-size:9pt; padding-left:6pt;">Telepon</td>
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
    <table style="width:100%; border-collapse:collapse; margin-top:3pt;">
        <tr>
            <td style="width:15pt; font-size:9pt; vertical-align:top;">3.</td>
            <td style="width:135pt; font-size:9pt; vertical-align:top;">Jenis Kepindahan</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['relocation_type_code'] ?? '' }}</span>
                <table style="width:100%; border-collapse:collapse; margin-top:2pt;">
                    <tr>
                        <td style="width:50%; font-size:9pt; padding:1pt 0;">1. Kep. Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">3. Kep.Keluarga dan sbg Angg.Keluarga</td>
                    </tr>
                    <tr>
                        <td style="font-size:9pt; padding:1pt 0;">2. Kep.Keluarga dan seluruh Angg.Keluarga</td>
                        <td style="font-size:9pt; padding:1pt 0;">4. Anggota Keluarga</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:separate; border-spacing:0 2pt; margin-top:1pt;">
        <tr>
            <td style="width:15pt; font-size:9pt;">4.</td>
            <td style="width:135pt; font-size:9pt;">Status KK bagi yang tidak Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['origin_kk_status_code'] ?? '' }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
        <tr>
            <td style="font-size:9pt;">5.</td>
            <td style="font-size:9pt;">Status Nomor KK bagi yang Pindah</td>
            <td style="border:1px solid #000; padding:2pt 4pt;">
                <span class="opsi-box">{{ $form['destination_kk_status_code'] ?? '' }}</span>
                1. Numpang KK &nbsp;&nbsp;&nbsp;2. Membuat KK Baru &nbsp;&nbsp;&nbsp;3. Nomor KK Tetap
            </td>
        </tr>
    </table>

    <div class="sub-heading" style="margin-top:2pt;">6. Keluarga Yang Pindah</div>
    <table style="width:100%; border-collapse:collapse; border:1px solid #000; font-size:9pt; margin-top:4pt; page-break-inside: avoid;">
        <tr>
            <th style="border:1px solid #000; width:18pt; padding:0 2pt; line-height:1.05;">NO</th>
            <th style="border:1px solid #000; padding:0 2pt; line-height:1.05;">NIK</th>
            <th style="border:1px solid #000; padding:0 2pt; line-height:1.05;">NAMA</th>
            <th style="border:1px solid #000; width:60pt; padding:0 2pt; line-height:1.05;">MASA BERLAKU KTP S/D</th>
            <th style="border:1px solid #000; width:55pt; padding:0 2pt; line-height:1.05;">SHDK</th>
        </tr>
        @forelse (($form['family_members'] ?? []) as $i => $row)
            <tr>
                <td style="border:1px solid #000; padding:1pt 2pt; height:11pt; line-height:1.1;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;">{{ $row['nik'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;">{{ $row['name'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;">{{ $row['valid_until'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;">{{ $row['shdk'] ?? '' }}</td>
            </tr>
        @empty
            @for ($i = 0; $i < 7; $i++)
                <tr>
                    <td style="border:1px solid #000; padding:1pt 2pt; height:11pt; line-height:1.1;">{{ $i + 1 }}</td>
                    <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;"></td>
                    <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;"></td>
                    <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;"></td>
                    <td style="border:1px solid #000; padding:0 2pt; line-height:1.05;"></td>
                </tr>
            @endfor
        @endforelse
    </table>

    {{-- ===== TTD: rata kiri di separuh kanan halaman, sesuai formulir asli ===== --}}
    <div style="page-break-inside: avoid;">
        <table class="ttd-left">
            <tr>
                <td style="width:50%;"></td>
                <td>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="font-size:9pt;">{{ $signature['city'] }}</td>
                            <td style="font-size:9pt; text-align:right;">{{ $signature['date_long'] }}</td>
                        </tr>
                    </table>
                    <div>Dikeluarkan Oleh</div>
                    <div>a.n Kepala Dinas Kependudukan Dan Pencatatan Sipil Kabupaten Sleman</div>
                    <div>Lurah</div>
                    <div class="ttd-space"></div>
                    <div>{{ $signature['name'] }}</div>
                </td>
            </tr>
        </table>
    </div>
@endsection