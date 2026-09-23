@extends('letters.layouts.form')

@section('title', 'Temporary Stay Application Form')

{{--
    No letterhead in the example, so the kop block is NOT called here.

    Expected variables:
    $stayApplication = [
        'applicantName'      => applicant's full name,
        'applicantNik'       => applicant's NIK,
        'familyCardNumber'   => KK number,
        'originAddress'      => applicant's home address,
        'originVillage'      => home village/kelurahan,
        'originDistrict'     => home district,
        'originRegency'      => home regency,
        'originProvince'     => home province,
        'reason'             => reason for the temporary stay,
        'destinationAddressLine1' => destination address (line 1, e.g. hamlet/location name),
        'destinationAddressLine2' => destination address (line 2, e.g. village, district, regency),
        'parentName'         => parent/guardian/close relative name,
        'parentAddress'      => parent/guardian address,
        'parentVillage'      => parent's village/kelurahan,
        'parentDistrict'     => parent's district,
        'parentRegency'      => parent's regency,
        'parentProvince'     => parent's province,
        'guarantorName'      => guarantor at the new address,
        'guarantorNik'       => guarantor's NIK,
        'guarantorAddress'   => guarantor's address,
        'guarantorVillage'   => guarantor's village/kelurahan,
        'guarantorDistrict'  => guarantor's district,
        'guarantorRegency'   => guarantor's regency,
        'guarantorProvince'  => guarantor's province,
        'familyMembers'      => [['name' => '', 'nik' => ''], ...] (up to 5 rows shown, rest left blank),
        'letterDate'         => letter date (e.g. "20 July 2026"),
        'applicantSignerName'=> applicant's name (right-hand signature),
        'applicantNumber'    => applicant's reference number (e.g. "471.23/"),
        'applicantDate'      => date next to the applicant's number,
        'hamletHeadNumber'   => hamlet head's (Dukuh) reference number, usually blank,
        'hamletHeadDate'     => hamlet head's date,
        'regency', 'district', 'village' => header block (default Sleman/Ngemplak/Bimomartani),
    ]
--}}

@section('extra_css')
    .tsa-header { font-size: 9.5pt; }
    table.tsa-header-tbl { border-collapse: collapse; }
    table.tsa-header-tbl td { padding: 0 4pt 0 0; vertical-align: top; }
    td.tsa-h-label { width: 62pt; }

    .tsa-title {
        text-align: center; font-weight: bold; text-decoration: underline;
        font-size: 11.5pt; margin-top: 10pt; margin-bottom: 10pt;
    }

    table.tsa-item { width: 100%; border-collapse: separate; border-spacing: 6pt; margin-top: 4pt; }
    table.tsa-item td { vertical-align: top; padding: 1pt 0; font-size: 9.5pt; }
    td.tsa-no { width: 16pt; }
    td.tsa-label { width: 175pt; }
    td.tsa-sep { width: 9pt; }

    table.tsa-item td.tsa-val {
        border: 1px solid #000;
        padding: 2pt 6pt 2pt 6pt !important;
        vertical-align: middle;
        line-height: normal;
    }
    .tsa-sub-row td.tsa-label { padding-left: 12pt; width: 163pt; }

    table.tsa-family { width: 100%; border-collapse: collapse; margin-top: 3pt; }
    table.tsa-family th, table.tsa-family td {
        border: 1px solid #000; padding: 2pt 6pt; font-size: 9pt;
    }
    table.tsa-family th { text-align: center; font-weight: bold; }
    td.fam-no { width: 22pt; text-align: center; }
    td.fam-nik { width: 40%; }

    table.tsa-family td.fam-blank { padding: 4pt 6pt !important; line-height: normal; }

    p.tsa-closing { margin-top: 8pt; font-size: 9.5pt; }

    /* ===================================================================
       WRAPPER untuk seluruh blok bawah, sama seperti trr — padding
       diperbesar (24pt) supaya blok ini "dimasukkan" lebih jauh dari tepi
       kiri/kanan halaman dibanding versi sebelumnya. */
    .tsa-bottom-wrap { padding: 0 24pt; margin-top: 2pt; }

    .tsa-place-date { text-align: right; font-size: 9.5pt; margin-top: 0; }

    /* ===================================================================
       Sama seperti trr-final: gabungkan blok tanda tangan (Dukuh/Pemohon)
       dengan blok Mengetahui/Nomor/Tanggal/PANEWU NGEMPLAK jadi SATU
       tabel, satu <tr> per baris visual, supaya kiri-kanan sejajar
       (sebelumnya dipisah jadi tsa-sign + tsa-bottom sehingga tidak
       sejajar). */
    table.tsa-final { width: 100%; border-collapse: collapse; margin-top: 6pt; }
    table.tsa-final td { width: 50%; vertical-align: top; font-size: 9.5pt; }
    .tsa-sign-space { height: 46pt; }
    td.tsa-sign-name { font-weight: bold; text-align: right; }

    table.tsa-bottom-inner { border-collapse: collapse; }
    table.tsa-bottom-inner td { padding: 0.5pt 4pt 0.5pt 0; }
    td.tbi-label { width: 45pt; }
@endsection

@section('content')

    <div class="tsa-header">
        <table class="tsa-header-tbl">
            <tr><td class="tsa-h-label">KABUPATEN</td><td>: {{ $stayApplication['regency'] ?? 'SLEMAN' }}</td></tr>
            <tr><td class="tsa-h-label">KECAMATAN</td><td>: {{ $stayApplication['district'] ?? 'NGEMPLAK' }}</td></tr>
            <tr><td class="tsa-h-label">DESA</td><td>: {{ $stayApplication['village'] ?? 'BIMOMARTANI' }}</td></tr>
        </table>
    </div>

    <div class="tsa-title">PERMOHONAN TINGGAL SEMENTARA</div>

    <table class="tsa-item">
        <tr>
            <td class="tsa-no">1.</td><td class="tsa-label">Nama Lengkap Pemohon</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['applicantName'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">2.</td><td class="tsa-label">NIK Pemohon</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['applicantNik'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">3.</td><td class="tsa-label">Nomor Kartu keluarga</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['familyCardNumber'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">4.</td><td class="tsa-label">Alamat Asal Pemohon</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['originAddress'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">a. Desa/Kelurahan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['originVillage'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">b. Kecamatan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['originDistrict'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">c. Kabupaten</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['originRegency'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">d. Provinsi</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['originProvince'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">5.</td><td class="tsa-label">Alasan Tinggal Sementara</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['reason'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">6.</td><td class="tsa-label">Alamat yang Dituju</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['destinationAddressLine1'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no"></td><td class="tsa-label"></td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['destinationAddressLine2'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">7.</td><td class="tsa-label">Nama Orang Tua/Wali/<br>Keluarga Dekat</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentName'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">8.</td><td class="tsa-label">Alamat Orang Tua/wali<br>Keluarga Dekat</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentAddress'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">a. Desa/Kelurahan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentVillage'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">b. Kecamatan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentDistrict'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">c. Kabupaten</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentRegency'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">d. Provinsi</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['parentProvince'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">9.</td><td class="tsa-label">Nama Penjamin Di Alamat Baru</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorName'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">10.</td><td class="tsa-label">NIK Penjamin</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorNik'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="tsa-no">11.</td><td class="tsa-label">Alamat Penjamin</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorAddress'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">a. Desa/Kelurahan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorVillage'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">b. Kecamatan</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorDistrict'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">c. Kabupaten</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorRegency'] ?? '' }}</td>
        </tr>
        <tr class="tsa-sub-row">
            <td class="tsa-no"></td><td class="tsa-label">d. Provinsi</td><td class="tsa-sep">:</td>
            <td class="tsa-val">{{ $stayApplication['guarantorProvince'] ?? '' }}</td>
        </tr>
    </table>

    <p style="margin-top:6pt; font-size:9.5pt;">
        12. Nama Dan NIK Anggota Keluarga Yang Ikut Tinggal Sementara
    </p>
    <table class="tsa-family">
        <tr>
            <th style="width:22pt;">No</th>
            <th>Nama</th>
            <th>NIK</th>
        </tr>
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td class="fam-no">{{ $i + 1 }}</td>
                <td class="fam-blank">{{ $stayApplication['familyMembers'][$i]['name'] ?? '' }}</td>
                <td class="fam-blank fam-nik">{{ $stayApplication['familyMembers'][$i]['nik'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <p class="tsa-closing">
        Demikian surat permohonan ini saya buat dengan sesungguhnya dan atas perhatianya diucapkan terima kasih.
    </p>

    {{-- Wrapper memberi jarak lebih besar (24pt) dari tepi kiri/kanan
         halaman untuk seluruh blok "Bimomartani..." sampai
         "PANEWU NGEMPLAK" di bawah ini. --}}
    <div class="tsa-bottom-wrap">

        <div class="tsa-place-date">Bimomartani, {{ $stayApplication['letterDate'] ?? '' }}</div>

        <table class="tsa-final">
            <tr>
                <td>Dukuh</td>
                <td style="text-align:right;">Pemohon</td>
            </tr>
            <tr>
                <td class="tsa-sign-space"></td>
                <td class="tsa-sign-space"></td>
            </tr>
            <tr>
                <td>Mengetahui</td>
                <td class="tsa-sign-name">{{ $stayApplication['applicantSignerName'] ?? '' }}</td>
            </tr>
            <tr>
                <td>
                    <table class="tsa-bottom-inner">
                        <tr><td class="tbi-label">Nomor</td><td>: {{ $stayApplication['hamletHeadNumber'] ?? '' }}</td></tr>
                    </table>
                </td>
                <td>
                    <table class="tsa-bottom-inner" style="margin-left:auto;">
                        <tr><td class="tbi-label">Nomor</td><td>: {{ $stayApplication['applicantNumber'] ?? '' }}</td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="tsa-bottom-inner">
                        <tr><td class="tbi-label">Tanggal</td><td>: {{ $stayApplication['hamletHeadDate'] ?? '' }}</td></tr>
                    </table>
                </td>
                <td>
                    <table class="tsa-bottom-inner" style="margin-left:auto;">
                        <tr><td class="tbi-label">Tanggal</td><td>: {{ $stayApplication['applicantDate'] ?? '' }}</td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>PANEWU NGEMPLAK</td>
                <td></td>
            </tr>
        </table>

    </div>

@endsection