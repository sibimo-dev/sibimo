@extends('letters.layouts.form')

@section('title', 'Temporary Resident Request Letter')

{{--
    No letterhead in the example (only the small form code handled by
    $kodeForm on the 'form' layout), so the kop block is NOT called here.

    Expected variables:
    $residentRequest = [
        'applicantName'       => applicant's name,
        'applicantNik'        => applicant's NIK,
        'birthInfo'           => place & date of birth,
        'gender'              => gender (L/P),
        'occupation'          => occupation,
        'education'           => education,
        'originAddress'       => home address,
        'originVillage'       => home village/kelurahan,
        'originDistrict'      => home district,
        'originRegency'       => home regency,
        'originProvince'      => home province,
        'religion'            => religion,
        'maritalStatus'       => marital status,
        'reason'              => reason,
        'destinationAddress'  => destination address,
        'rt'                  => RT,
        'rw'                  => RW,
        'hamlet'              => destination hamlet (dusun),
        'village'             => destination village/kalurahan (default Bimomartani),
        'district'            => destination district/kapanewon (default Ngemplak),
        'hostName'            => name of the family the applicant will join,
        'hostFamilyCardNumber'=> host's KK number,
        'familyMemberCount'   => number of family members joining,
        'familyMembers'       => [['name' => '', 'nik' => ''], ...] (up to 5 rows shown),
        'letterDate'          => letter date,
        'applicantSignerName' => applicant's name (right-hand signature),
        'applicantNumber'     => applicant's reference number,
        'applicantDate'       => date next to the applicant's number,
        'hamletHeadNumber'    => hamlet head's (Dukuh) reference number,
        'hamletHeadDate'      => hamlet head's date,
    ]
--}}

@section('extra_css')
    .trr-body { font-family: 'Times New Roman', Times, serif; font-size: 11.5pt; line-height: 1.35; }

    .trr-hal { margin-top: 3pt; }
    .trr-to { margin-top: 12pt; }
    .trr-to div { margin-top: 0; }
    .trr-greeting { margin-top: 12pt; }
    .trr-intro { margin-top: 3pt; }

    table.trr-item { border-collapse: collapse; margin-top: 4pt; margin-left: 20pt; }
    table.trr-item td { vertical-align: top; padding: 1pt 0; }
    td.trr-label { width: 130pt; }
    td.trr-sep { width: 10pt; }
    td.trr-val { padding-left: 3pt; }

    .trr-request { margin-top: 7pt; }

    table.trr-dest { border-collapse: collapse; margin-top: 1pt; margin-left: 20pt; }
    table.trr-dest td { padding: 1pt 0; vertical-align: top; }
    td.trr-d-label { width: 130pt; }
    td.trr-d-sep { width: 10pt; }
    td.trr-d-val { width: 95pt; padding-left: 3pt; }
    td.trr-d-label2 { width: 62pt; padding-left: 8pt; }
    td.trr-d-sep2 { width: 10pt; }

    p.trr-plain { margin-top: 3pt; margin-left: 20pt; }

    /* Tabel keluarga: diberi margin-left 20pt yang SAMA dengan trr-item,
       supaya sisi kiri tabel sejajar dengan teks "Kami akan menjadi
       keluarga dari" dkk di atasnya. Lebar dikurangi (calc(100% - 20pt))
       supaya total lebar tabel + margin-left tetap pas di dalam batas
       halaman, tidak melebar sampai ke tepi kanan seperti sebelumnya. */
    table.trr-family { width: calc(100% - 20pt); margin-left: 20pt; border-collapse: collapse; margin-top: 7pt; }
    table.trr-family th, table.trr-family td {
        border: 1px solid #000; padding: 3pt 6pt; font-size: 10.5pt;
    }
    table.trr-family th { text-align: center; font-weight: bold; }
    td.fam-no { width: 26pt; text-align: center; }
    .fam-blank { height: 15pt; }

    table.trr-family { page-break-inside: avoid; }
    table.trr-family tr { page-break-inside: avoid; }

    p.trr-closing { margin-top: 12pt; line-height: 1.35; }

    .trr-bottom-wrap { padding: 0 20pt; margin-top: 3pt; }

    .trr-place-date { text-align: right; margin-top: 0; }

    table.trr-final { width: 100%; border-collapse: collapse; margin-top: 9pt; }
    table.trr-final td { width: 50%; vertical-align: top; }
    .trr-sign-space { height: 44pt; }
    td.trr-sign-name { font-weight: bold; text-align: right; }

    table.trr-bottom-inner { border-collapse: collapse; }
    table.trr-bottom-inner td { padding: 1.5pt 0; }
    td.trr-bottom-right table.trr-bottom-inner { margin-left: auto; }
    td.tbi-label { width: 52pt; }
    td.tbi-sep { width: 9pt; }
    td.tbi-val { width: 120pt; padding-left: 3pt; }
@endsection

@section('content')
<div class="trr-body">

    <div class="trr-hal">Hal&nbsp;&nbsp;: Permohonan Menjadi Penduduk Sementara</div>

    <div class="trr-to">
        Kepada<br>
        Yth. Bupati Sleman<br>
        Di Sleman
    </div>

    <div class="trr-greeting">Dengan hormat</div>
    <div class="trr-intro">Yang bertanda tangan di bawah ini saya,</div>

    <table class="trr-item">
        <tr>
            <td class="trr-label">N a m a</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['applicantName'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">NIK</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['applicantNik'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Tempat&amp;Tgl Lahir</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['birthInfo'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Jenis Kelamin</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['gender'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Pekerjaan</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['occupation'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Pendidikan</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['education'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Alamat Asal</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['originAddress'] ?? '' }}</td>
        </tr>
    </table>

    <table class="trr-dest">
        <tr>
            <td class="trr-d-label">Desa/Kel</td><td class="trr-d-sep">:</td>
            <td class="trr-d-val">{{ $residentRequest['originVillage'] ?? '' }}</td>
            <td class="trr-d-label2">Kecamatan</td><td class="trr-d-sep2">:</td>
            <td class="trr-d-val">{{ $residentRequest['originDistrict'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-d-label">Kabupaten</td><td class="trr-d-sep">:</td>
            <td class="trr-d-val">{{ $residentRequest['originRegency'] ?? '' }}</td>
            <td class="trr-d-label2">Provinsi</td><td class="trr-d-sep2">:</td>
            <td class="trr-d-val">{{ $residentRequest['originProvince'] ?? '' }}</td>
        </tr>
    </table>

    <table class="trr-item">
        <tr>
            <td class="trr-label">Agama</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['religion'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Status</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['maritalStatus'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Alasan</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['reason'] ?? '' }}</td>
        </tr>
    </table>

    <p class="trr-request">Mengajukan permohonan untuk menjadi penduduk Sementara di</p>

    <table class="trr-item">
        <tr>
            <td class="trr-label">Alamat</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['destinationAddress'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">RT / RW</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['rt'] ?? '' }} / {{ $residentRequest['rw'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Dusun</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['hamlet'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label"></td><td class="trr-sep">:</td>
            <td class="trr-val">
                Kalurahan {{ $residentRequest['village'] ?? 'Bimomartani' }}, Kapanewon {{ $residentRequest['district'] ?? 'Ngemplak' }}
            </td>
        </tr>
    </table>

    <table class="trr-item">
        <tr>
            <td class="trr-label">Kami akan menjadi keluarga dari</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['hostName'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Nomor KK</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['hostFamilyCardNumber'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="trr-label">Keluarga yang turut</td><td class="trr-sep">:</td>
            <td class="trr-val">{{ $residentRequest['familyMemberCount'] ?? '' }} Orang</td>
        </tr>
    </table>

    <table class="trr-family">
        <tr>
            <th style="width:26pt;">No</th>
            <th>Nama</th>
            <th>NIK</th>
        </tr>
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td class="fam-no">{{ $i + 1 }}</td>
                <td class="fam-blank">{{ $residentRequest['familyMembers'][$i]['name'] ?? '' }}</td>
                <td class="fam-blank">{{ $residentRequest['familyMembers'][$i]['nik'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <p class="trr-closing">
        Demikian surat permohonan ini saya buat dengan sesungguhnya dan atas perhatianya diucapkan terima kasih.
    </p>

    <div class="trr-bottom-wrap">

        <div class="trr-place-date">Bimomartani, {{ $residentRequest['letterDate'] ?? '' }}</div>

        <table class="trr-final">
            <tr>
                <td>Dukuh</td>
                <td style="text-align:right;">Pemohon</td>
            </tr>
            <tr>
                <td class="trr-sign-space"></td>
                <td class="trr-sign-space"></td>
            </tr>
            <tr>
                <td>Mengetahui</td>
                <td class="trr-sign-name">{{ $residentRequest['applicantSignerName'] ?? '' }}</td>
            </tr>
            <tr>
                <td>
                    <table class="trr-bottom-inner">
                        <tr>
                            <td class="tbi-label">Nomor</td><td class="tbi-sep">:</td>
                            <td class="tbi-val">{{ $residentRequest['hamletHeadNumber'] ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="trr-bottom-right">
                    <table class="trr-bottom-inner">
                        <tr>
                            <td class="tbi-label">Nomor</td><td class="tbi-sep">:</td>
                            <td class="tbi-val">{{ $residentRequest['applicantNumber'] ?? '' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="trr-bottom-inner">
                        <tr>
                            <td class="tbi-label">Tanggal</td><td class="tbi-sep">:</td>
                            <td class="tbi-val">{{ $residentRequest['hamletHeadDate'] ?? '' }}</td>
                        </tr>
                    </table>
                </td>
                <td class="trr-bottom-right">
                    <table class="trr-bottom-inner">
                        <tr>
                            <td class="tbi-label">Tanggal</td><td class="tbi-sep">:</td>
                            <td class="tbi-val">{{ $residentRequest['applicantDate'] ?? '' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>PANEWU NGEMPLAK</td>
                <td></td>
            </tr>
        </table>

    </div>

</div>
@endsection