{{--
    Surat Pernyataan Belum Menikah Lagi.
    Isi kolom letter_types.blade_view dengan: letters.not-remarried-statement-letter

    File ini BERDIRI SENDIRI (tidak @extends layout kop) — pernyataan pribadi pemohon,
    diketahui/dicap oleh perangkat desa (Kamituwa) di kolom kiri.

    Data: $registration (number, date, position, officer_name),
          $applicant (name, nik, gender, birth_place_date, nationality, religion,
                      occupation, last_education, address, marital_status),
          $signature (city, date_long)
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Belum Menikah Lagi</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.3; color:#000; }
        p { margin: 0; }
        .title { text-align:center; font-weight:bold; text-decoration:underline; margin-bottom: 14pt; }
        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.no { width: 16pt; }
        td.lbl { width: 165pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }
        p.gap-top { margin-top: 10pt; }
        ol.terms { margin: 6pt 0 0 18pt; padding: 0; }
        ol.terms li { margin-bottom: 2pt; text-align: justify; }

        table.footer { width:100%; border-collapse:collapse; margin-top: 20pt; }
        table.footer td { width:50%; vertical-align:top; }
        .footer-right { text-align:right; }
        .footer-space { height: 34pt; }
        .center-signer { width:71%; vertical-align:top; }
    </style>
</head>
<body>

    <div class="title">SURAT PERNYATAAN BELUM MENIKAH LAGI</div>

    <p>Yang bertanda tangan di bawah ini, saya :</p>
    <table class="data">
        <tr><td class="no">1.</td><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $applicant['name'] ?? '' }}</td></tr>
        <tr><td class="no">2.</td><td class="lbl">NIK</td><td class="sep">:</td><td class="val">{{ $applicant['nik'] ?? '' }}</td></tr>
        <tr><td class="no">3.</td><td class="lbl">Jenis Kelamin</td><td class="sep">:</td><td class="val">{{ $applicant['gender'] ?? '' }}</td></tr>
        <tr><td class="no">4.</td><td class="lbl">Tempat dan tanggal lahir</td><td class="sep">:</td><td class="val">{{ $applicant['birth_place_date'] ?? '' }}</td></tr>
        <tr><td class="no">5.</td><td class="lbl">Warganegara</td><td class="sep">:</td><td class="val">{{ $applicant['nationality'] ?? '' }}</td></tr>
        <tr><td class="no">6.</td><td class="lbl">Agama</td><td class="sep">:</td><td class="val">{{ $applicant['religion'] ?? '' }}</td></tr>
        <tr><td class="no">7.</td><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $applicant['occupation'] ?? '' }}</td></tr>
        <tr><td class="no">8.</td><td class="lbl">Pendidikan Terakhir</td><td class="sep">:</td><td class="val">{{ $applicant['last_education'] ?? '' }}</td></tr>
        <tr><td class="no">9.</td><td class="lbl">Alamat</td><td class="sep">:</td><td class="val">{{ $applicant['address'] ?? '' }}</td></tr>
        <tr><td class="no">10.</td><td class="lbl">Status Perkawinan</td><td class="sep">:</td><td class="val">{{ $applicant['marital_status'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top">dengan ini menyatakan sebenar-benarnya bahwa:</p>
    <ol class="terms">
        <li>saya belum menikah lagi</li>
        <li>kebenaran materi/isi surat pernyataan ini sepenuhnya menjadi tanggung jawab saya</li>
        <li>apabila di kemudian hari ternyata pernyataan saya ini tidak benar, maka saya bersedia
            diproses secara hukum sesuai ketentuan peraturan perundang-undangan yang berlaku.</li>
    </ol>

    <p class="gap-top">Demikian surat pernyataan ini dibuat untuk dapat digunakan sebagaimana mestinya.</p>

    <table class="footer">
        <tr>
            <td>
                <div>NO: {{ $registration['number'] ?? '' }}</div>
                <div>TGL: {{ $registration['date'] ?? '' }}</div>
                <div>KAMITUWA</div>
            </td>
            <td class="footer-right">
                <div>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</div>
                <div class="center-signer">Yang Menyatakan</div>
            </td>
        </tr>
        <tr>
            <td><div class="footer-space">&nbsp;</div></td>
            <td class="footer-right"><div class="footer-space">&nbsp;</div></td>
        </tr>
        <tr>
            <td>{{ $registration['officer_name'] ?? '' }}</td>
            <td class="footer-right">{{ $applicant['name'] ?? '' }}</td>
        </tr>
    </table>

</body>
</html>