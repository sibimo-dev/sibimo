<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan</title>
    <style>
        @page { margin: 2cm 2cm 2cm 2cm; }

        body {
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11.4pt;
            line-height: 1.25;
            color: #000;
        }

        p {
            margin: 0;
        }

        table.kop {
            width: 100%;
            border-collapse: collapse;
        }

        table.kop td {
            padding: 0;
            vertical-align: top;
        }

        table.kop td.kop-logo {
            width: 75pt;
            padding-top: 6pt;
        }

        td.kop-logo img {
            width: 63pt;
            height: 82pt;
        }

        table.kop td.kop-text {
            text-align: center;
            padding-top: 9pt;
        }

        .kop-1 {
            font-size: 13.3pt;
            font-weight: bold;
            line-height: 11.5pt;
            font-family: Helvetica, Arial, sans-serif;
        }

        .kop-2 {
            font-size: 15.2pt;
            font-weight: bold;
            line-height: 21pt;
            font-family: Helvetica, Arial, sans-serif;
        }

        .kop-3 {
            font-size: 17.1pt;
            font-weight: bold;
            line-height: 20pt;
            font-family: Times, serif;
        }

        .kop-aksara {
            margin-top: -2pt;
            margin-bottom: -3pt;
        }

        .kop-aksara img {
            width: 250pt;
            height: auto;
        }

        .kop-info {
            font-size: 11.4pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .kop-line {
            border-bottom: 2pt solid #000;
            margin-top: 18pt;
        }

        .title {
            margin-top: 10pt;
            text-align: center;
            font-size: 13.3pt;
            font-weight: bold;
            text-decoration: underline;
        }

        .intro {
            margin-top: 12pt;
        }

        table.data {
            width: 388pt;
            margin-left: 66pt;
            border-collapse: collapse;
            margin-top: 12pt;
        }

        table.data td {
            padding: 2.5pt 0;
            vertical-align: top;
        }

       table.data td.no {
            width: 20pt;
            min-width: 20pt;
            padding: 0;
            text-align: right;
            white-space: nowrap;
            padding-right: 10pt;
        }

        table.data td.lbl {
            width: 125pt;
            min-width: 125pt;
            padding: 0;
            text-align: left;
            white-space: nowrap;
        }

        table.data td.sep {
            width: 12pt;
            min-width: 12pt;
            padding: 0;
            text-align: left;
        }

        table.data td.val {
            padding: 0 0 0 2pt;
            height: 14pt;
            border-bottom: 1px dashed #000;
        }

        table.data tr.sub-row td {
            padding-top: 2.5pt;
            padding-bottom: 2.5pt;
        }

        table.data tr.sub-row td.no {
            width: 20pt;
            min-width: 20pt;
        }

        table.data tr.sub-row td.lbl {
            width: 125pt;
            min-width: 125pt;
        }

        .validity {
            width: 454pt;
            margin-left: 0pt;
            margin-top: 8pt;
        }

        .validity table {
            width: 100%;
            border-collapse: collapse;
        }

        .validity-label {
            width: 221pt;
            padding: 0;
            white-space: nowrap;
        }

        .validity-sep {
            width: 12pt;
            padding: 0;
        }

        .validity-value {
            height: 14pt;
            padding: 0 0 0 2pt;
            border-bottom: 1px dashed #000;
        }

        .closing {
            margin-top: 10pt;
        }

        table.signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18pt;
        }

        table.signature td {
            width: 50%;
            vertical-align: top;
        }

        .signature-left {
            padding-left: 38pt;
        }

        .signature-right {
            text-align: left;
            padding-left: 55pt;
        }

        .signature-space {
            height: 38pt;
        }
    </style>
</head>
<body>

    <table class="kop">
        <tr>
            <td class="kop-logo">
                <img src="{{ $logo ?? '' }}" alt="Logo Kabupaten Sleman">
            </td>

            <td class="kop-text">
                <div class="kop-1">
                    {{ $kop['line1'] ?? 'PEMERINTAH KABUPATEN SLEMAN' }}
                </div>

                <div class="kop-2">
                    {{ $kop['line2'] ?? 'KAPANEWON NGEMPLAK' }}
                </div>

                <div class="kop-3">
                    {{ $kop['line3'] ?? 'PEMERINTAH KALURAHAN BIMOMARTANI' }}
                </div>

                <div class="kop-aksara">
                    <img src="{{ $kop['aksara'] ?? '' }}" alt="">
                </div>

                <div class="kop-info">
                    {{ $kop['address'] ?? '' }}
                </div>

                <div class="kop-info">
                    {{ $kop['contact'] ?? '' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="kop-line"></div>

    <div class="title">
        SURAT KETERANGAN
    </div>

    <p class="intro">
        Menerangkan bahwa orang tersebut dibawah ini:
    </p>

    <table class="data">
        <tr>
            <td class="no">1</td>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">2</td>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['gender'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">3</td>
            <td class="lbl">Tempat/Tgl Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth_place_date'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">4</td>
            <td class="lbl">Nik</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['nik'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">5</td>
            <td class="lbl">Kewarganegaraan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['nationality'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">6</td>
            <td class="lbl">Agama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['religion'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">7</td>
            <td class="lbl">Pendidikan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['education'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">8</td>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['occupation'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">9</td>
            <td class="lbl">Status</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['status'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">10</td>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">11</td>
            <td class="lbl">Kelakuan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['behavior'] ?? '' }}</td>
        </tr>

        <tr>
            <td class="no">12</td>
            <td class="lbl">Tujuan ke</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['purpose'] ?? '' }}</td>
        </tr>

        <tr class="sub-row">
            <td class="no"></td>
            <td class="lbl">Keperluan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['purpose_detail'] ?? '' }}</td>
        </tr>

        <tr class="sub-row">
            <td class="no"></td>
            <td class="lbl">Keterangan lain-lain</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['additional_note'] ?? '' }}</td>
        </tr>
    </table>

    <div class="validity">
        <table>
            <tr>
                <td class="validity-label"> Surat pengantar ini berlaku sampai tanggal </td>

                <td class="validity-sep"> : </td>

                <td class="validity-value">
                    {{ $applicant['valid_until'] ?? '' }}
                </td>
            </tr>
        </table>
    </div>

    <p class="closing">
        Demikian pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.
    </p>

    <table class="signature">
        <tr>
            <td></td>
            <td class="signature-right">
                {{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}
            </td>
        </tr>

        <tr>
            <td class="signature-left">
                Pemegang
            </td>

            <td class="signature-right">
                {{ $signature['position'] ?? '' }}
            </td>
        </tr>

        <tr>
            <td>
                <div class="signature-space">&nbsp;</div>
            </td>

            <td>
                <div class="signature-space">&nbsp;</div>
            </td>
        </tr>

        <tr>
            <td class="signature-left">
                {{ $signature['holder_name'] ?? '' }}
            </td>

            <td class="signature-right">
                {{ $signature['signer_name'] ?? '' }}
            </td>
        </tr>
    </table>

</body>
</html>