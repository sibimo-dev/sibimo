<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Tanggung Jawab Mutlak Perkawinan Belum Tercatat</title>
    <style>
        /* Margin halaman untuk DomPDF */
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }

        body {
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.3pt;
            line-height: 1.25;
            color: #000;
        }

        p { margin: 0; }

        /* ===== KOTAK KODE FORMULIR (kanan atas) ===== */
        table.form-code-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
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
            margin-top: 4pt;
            margin-bottom: 14pt;
        }

        /* ===== TABEL DATA "Label : Nilai" ===== */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2pt;
        }
        table.data td { padding: 1.2pt 0; vertical-align: top; }
        td.lbl { width: 155pt; }
        td.sep { width: 12pt; }
        td.val {
            border-bottom: 1px dashed #000;
            padding-left: 3pt;
            height: 13pt;
        }

        .party-label { margin-top: 3pt; margin-bottom: 4pt; }
        p.gap-top { margin-top: 8pt; }

        .inline-blank {
            display: inline-block;
            min-width: 90pt;
            border-bottom: 1px dashed #000;
            padding: 0 3pt;
        }

        table.witness td.no { width: 16pt; }

        /* ===== TABEL ANAK ===== */
        table.children {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6pt;
        }
        table.children th, table.children td {
            border: 1px solid #000;
            padding: 2pt 4pt;
            font-size: 10pt;
        }
        table.children th { text-align: center; font-weight: bold; }
        table.children td.no { width: 24pt; text-align: center; }
        table.children td.shdk { width: 60pt; text-align: center; }

        /* ===== TANDA TANGAN ===== */
        .signature-date {
            text-align: right;
            margin-top: 14pt;
        }
        .signature-label {
            text-align: center;
            margin-top: 1pt;
        }
        table.sig-cols {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4pt;
        }
        table.sig-cols td { text-align: center; vertical-align: top; padding: 0 6pt; }
        td.sig-col { width: 47%; }
        td.sig-gap { width: 6%; }
        .materai-box {
            border: 1pt solid #000;
            width: 60pt;
            height: 30pt;
            margin: 2pt 0 0 0;
            text-align: center;
            font-size: 7.5pt;
            line-height: 10pt;
            padding-top: 4pt;
        }
        .sig-space { height: 34pt; }
        .sig-line-mark {
            display: inline-block;
            width: 170pt;
            border-top: 1px dashed #000;
        }
        .sig-left { text-align: left !important; }
    </style>
</head>
<body>

    {{-- ===== KODE FORMULIR ===== --}}
    <table class="form-code-row">
        <tr>
            <td>
                <div class="form-code-box">{{ $form_code ?? 'F.1.07' }}</div>
            </td>
        </tr>
    </table>

    {{-- ===== JUDUL ===== --}}
    <div class="title">
        SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK<br>
        PERKAWINAN BELUM TERCATAT
    </div>

    {{-- ===== ISI ===== --}}
    <p>Kami yang bertanda tangan dibawah ini</p>
    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $husband['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $husband['nik'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat Dan Tanggal Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $husband['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $husband['occupation'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $husband['address'] }}</td>
        </tr>
    </table>
    <p class="party-label">sebagai suami, selanjutnya disebut <strong>PIHAK PERTAMA</strong></p>

    <table class="data">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $wife['name'] }}</td>
        </tr>
        <tr>
            <td class="lbl">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $wife['nik'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Tempat Dan Tanggal Lahir</td>
            <td class="sep">:</td>
            <td class="val">{{ $wife['birth'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $wife['occupation'] }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $wife['address'] }}</td>
        </tr>
    </table>
    <p class="party-label">sebagai istri, selanjutnya disebut <strong>PIHAK KEDUA</strong></p>

    <p>
        menyatakan bahwa kami telah terkait perkawinan sebagai suami istri telah melakukan
        Perkawinan yang dilaksanakan pada
        <span class="inline-blank">{{ $marriage_date }}</span>
        dengan saksi-saksi:
    </p>

    <table class="data witness">
        @foreach (($marriage_witnesses ?? [null, null]) as $i => $witness)
            <tr>
                <td class="no">{{ ['I', 'II', 'III', 'IV'][$i] ?? ($i + 1) }}.</td>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['name'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="no">&nbsp;</td>
                <td class="lbl">NIK</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['nik'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>

    <p class="gap-top">Dengan nama anak-anak sebagai berikut</p>
    <table class="children">
        <tr>
            <th style="width: 24pt;">No.</th>
            <th>Nama</th>
            <th>Nomer Akta Kelahiran</th>
            <th style="width: 60pt;">SHDK</th>
        </tr>
        @php $rows = max(7, count($children ?? [])); @endphp
        @for ($i = 0; $i < $rows; $i++)
            <tr>
                <td class="no">{{ $i + 1 }}</td>
                <td>{{ $children[$i]['name'] ?? '' }}</td>
                <td>{{ $children[$i]['birth_cert_number'] ?? '' }}</td>
                <td class="shdk">{{ $children[$i]['shdk'] ?? '' }}</td>
            </tr>
        @endfor
    </table>

    <p class="gap-top">
        Demikian Surat penyataan ini kami buat dengan sebenarnya, apabila dalam keterangan yang
        kami berikan terdapat hal-hal yang tidak berdasarkan keadaan yang sebenarnya, kami bersedia
        dikenakan sanksi sesuai dengan ketentuan peraturan perundang-undangan yang berlaku
    </p>

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="signature-date">{{ $signature['city'] }}, {{ $signature['date_long'] }}</div>
    <div class="signature-label">Yang menyatakan</div>

    <table class="sig-cols">
        <tr>
            <td class="sig-col">PIHAK KEDUA</td>
            <td class="sig-gap"></td>
            <td class="sig-col">PIHAK PERTAMA</td>
        </tr>
        <tr>
            <td class="sig-col"><div class="sig-space">&nbsp;</div></td>
            <td class="sig-gap"></td>
            <td class="sig-col sig-left"><div class="materai-box">Materai<br>Cukup</div></td>
        </tr>
        <tr>
            <td class="sig-col"><div class="sig-line-mark">&nbsp;</div></td>
            <td class="sig-gap"></td>
            <td class="sig-col"><div class="sig-line-mark">&nbsp;</div></td>
        </tr>
    </table>

    <table class="sig-cols" style="margin-top: 16pt;">
        <tr>
            <td class="sig-col">SAKSI II</td>
            <td class="sig-gap"></td>
            <td class="sig-col">SAKSI I</td>
        </tr>
        <tr>
            <td class="sig-col"><div class="sig-space">&nbsp;</div></td>
            <td class="sig-gap"></td>
            <td class="sig-col"><div class="sig-space">&nbsp;</div></td>
        </tr>
        <tr>
            <td class="sig-col"><div class="sig-line-mark">&nbsp;</div></td>
            <td class="sig-gap"></td>
            <td class="sig-col"><div class="sig-line-mark">&nbsp;</div></td>
        </tr>
    </table>

</body>
</html>