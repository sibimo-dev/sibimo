<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Kuasa</title>
    <style>
        @page {
            margin: 2cm 2.3cm 1.6cm 2.3cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', serif;
            font-size: 10.5pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 12.5pt;
            letter-spacing: 1px;
            margin: 0 0 12px 0;
        }

        p {
            text-align: justify;
            margin: 6px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0 8px 0;
        }

        table.data td {
            padding: 0 4px 2px 4px;
            vertical-align: bottom;
            font-size: 10.5pt;
        }

        table.data td.lbl {
            width: 140px;
            padding-bottom: 2px;
        }

        table.data td.sep {
            width: 12px;
            padding-bottom: 2px;
        }

        table.data td.val {
            border-bottom: 1px dotted #000;
        }

        table.khusus {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table.khusus td {
            padding: 0;
        }

        table.khusus td.line {
            border-top: 1px solid #000;
        }

        table.khusus td.label {
            width: 110px;
            text-align: center;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 0 10px;
        }

        table.list {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        table.list td {
            padding: 1px 0;
            vertical-align: top;
            text-align: justify;
        }

        table.list td.num {
            width: 20px;
            vertical-align: top;
        }

        .date-right {
            text-align: right;
            margin: 14px 0 18px 0;
        }

        table.signature {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        table.signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
        }

        .materai {
            width: 80px;
            height: 45px;
            margin: 5px auto;
            border: 1px solid #000;
            text-align: center;
            font-size: 8pt;
            line-height: 1.2;
            padding-top: 12px;
        }

        .materai-placeholder {
            height: 61px;
        }

        .sign-space {
            height: 34px;
        }

        .mengetahui {
            text-align: center;
            margin-top: 18px;
        }

        .mengetahui p {
            text-align: center;
            margin: 0;
        }

        .mengetahui .sign-space {
            height: 95px;
        }
    </style>
</head>
<body>

    <div class="title">SURAT KUASA</div>

    <p>Saya yang bertanda tangan dibawah ini:</p>

    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tmpt/tgl. Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['gender'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['nik'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
    </table>

    <p>
        Dalam hal ini bertindak sebagai dan atas nama ahli waris dari
        Almarhum/Almarhumah <strong>{{ $deceased['name'] }}</strong>,
        yang telah meninggal dunia di {{ $deceased['death_place'] }},
        untuk berikutnya disebut <strong>PIHAK PERTAMA</strong>.
    </p>

    <p>Dengan ini memberikan kuasa kepada pihak <strong>KEDUA</strong>:</p>

    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $attorney['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tmpt/tgl. Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $attorney['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Jenis Kelamin</td>
            <td class="sep">:</td>
            <td class="val">{{ $attorney['gender'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $attorney['nik'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $attorney['address'] }}</td>
        </tr>
    </table>

    <table class="khusus">
        <tr>
            <td class="line"></td>
            <td class="label">KHUSUS</td>
            <td class="line"></td>
        </tr>
    </table>

    <p>
        Untuk dan atas nama <strong>PIHAK PERTAMA</strong> yang telah disebut diatas,
        maka <strong>PIHAK KEDUA</strong> dapat melakukan hal sebagai berikut:
    </p>

    <table class="list">
        <tr>
            <td class="num">1)</td>
            <td>
                Mewakili <strong>PIHAK PERTAMA</strong> untuk hadir dalam sidang waris
                di Kelurahan {{ $village }}.
            </td>
        </tr>
        <tr>
            <td class="num">2)</td>
            <td>
                Mewakili <strong>PIHAK PERTAMA</strong> untuk menandatangani buku
                sidang waris di Kelurahan {{ $village }}.
            </td>
        </tr>
        <tr>
            <td class="num">3)</td>
            <td>
                Mewakili <strong>PIHAK PERTAMA</strong> mengurus dokumen pertanahan/berkas
                turun waris di Kelurahan {{ $village }}.
            </td>
        </tr>
    </table>

    <p>
        Demikian surat kuasa ini dibuat dengan sebenar-benarnya dan bisa digunakan
        sebagaimana mestinya.
    </p>

    <p class="date-right">.......................................... {{ $year }}</p>

    <table class="signature">
        <tr>
            <td>
                Penerima Kuasa
                <div class="materai-placeholder"></div>
                <div class="sign-space"></div>
                (....................................)
            </td>
            <td>
                Pemberi Kuasa
                <div class="materai">Materai<br>Rp. 10.000</div>
                <div class="sign-space"></div>
                (....................................)
            </td>
        </tr>
    </table>

    <div class="mengetahui">
        <p>Mengetahui</p>
        <p>Kelurahan/Notaris..........................</p>
        <div class="sign-space"></div>
        (....................................)
    </div>

</body>
</html>