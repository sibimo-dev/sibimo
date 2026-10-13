<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Tidak Memiliki Dokumen Kependudukan</title>
    <style>
        /* Margin halaman untuk DomPDF */
        @page { margin: 2cm 2cm 2cm 2cm; }

        body {
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        p { margin: 0; }

        /* ===== KOTAK KODE FORMULIR (kanan atas) ===== */
        table.form-code-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }
        table.form-code-row td { padding: 0; vertical-align: top; }
        .form-code-box {
            float: right;
            border: 1pt solid #000;
            padding: 2pt 10pt;
            font-weight: bold;
            text-align: center;
        }

        /* ===== JUDUL ===== */
        .title {
            clear: both;
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 6pt;
            margin-bottom: 20pt;
        }

        /* ===== TABEL DATA "Label : Nilai" ===== */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
        }
        table.data td { padding: 2pt 0; vertical-align: top; }
        td.lbl { width: 150pt; }
        td.sep { width: 12pt; }
        td.val {
            border-bottom: 1px dashed #000;
            padding-left: 3pt;
            height: 14pt;
        }
        .gap-row td { height: 12pt; border-bottom: none; }

        /* ===== PARAGRAF PERNYATAAN ===== */
        p.statement {
            text-align: justify;
            margin-top: 16pt;
        }

        /* ===== TANDA TANGAN ===== */
        .signature-block {
            margin-top: 20pt;
            margin-left: 260pt;
        }
        .signature-block div { line-height: 18pt; }
        .materai-box {
            border: 1pt solid #000;
            width: 80pt;
            height: 40pt;
            margin-top: 4pt;
            text-align: center;
            font-size: 8pt;
            line-height: 12pt;
            padding-top: 6pt;
        }
        .signature-space { height: 40pt; }
    </style>
</head>
<body>

    {{-- ===== KODE FORMULIR ===== --}}
    <table class="form-code-row">
        <tr>
            <td>
                <div class="form-code-box">{{ $form_code ?? 'F.1-04' }}</div>
            </td>
        </tr>
    </table>

    {{-- ===== JUDUL ===== --}}
    <div class="title">SURAT PERNYATAAN TIDAK MEMILIKI DOKUMEN KEPENDUDUKAN</div>

    {{-- ===== ISI ===== --}}
    <p>Yang bertanda tangan dibawah ini :</p>
    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
        <tr>
            <td class="lbl">&nbsp;</td>
            <td class="sep">&nbsp;</td>
            <td class="val">&nbsp;</td>
        </tr>
        <tr class="gap-row">
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td class="lbl">Tempat Dan Tanggal Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Nama Ibu</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['mother_name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Nama Ayah</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['father_name'] }}</td>
        </tr>
    </table>

    <p class="statement">
        Dengan ini menyatakan bahwa saya tidak memiliki dokumen kependudukan dan apabila
        dikemudian hari ternyata pernyataan saya ini tidak benar, maka saya bersedia diproses secara
        hukum sesuai dengan peraturan perundang-undangan yang berlaku serta dokumen yang
        diterbitkan dari permohonan ini menjadi tidak sah
    </p>

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="signature-block">
        <div>{{ $signature['city'] }}, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $signature['date_long'] }}</div>
        <div>Yang Membuat Penyataan</div>
        <div class="materai-box">Materai<br>Cukup</div>
        <div class="signature-space">&nbsp;</div>
    </div>

</body>
</html>