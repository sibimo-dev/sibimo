<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Penyataan Beda Nama/Identitas</title>
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

        /* ===== JUDUL ===== */
        .title {
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
            margin-left: 24pt;
        }
        table.data td { padding: 2pt 0; vertical-align: top; }
        td.label { width: 150pt; }
        td.sep { width: 12pt; }
        td.val {
            border-bottom: 1px dashed #000;
            padding-left: 3pt;
            height: 14pt;
        }

        p.gap-top { margin-top: 14pt; }

        /* ===== TANDA TANGAN ===== */
        /* Semua elemen di dalam blok ini rata kiri pada tepi yang sama */
        .signature-block {
            margin-top: 20pt;
            margin-left: 260pt;
            width: 190pt;
        }
        .signature-block .line { line-height: 18pt; }

        .stamp-duty-box {
            border: 1pt solid #000;
            width: 80pt;
            height: 34pt;
            margin-top: 4pt;
            text-align: center;
            font-size: 8pt;
            line-height: 12pt;
            padding-top: 6pt;
        }

        /* Nama pemohon: ruang kosong di atas untuk tanda tangan, garis putus-putus di bawah */
        .sign-name {
            width: 170pt;
            margin-top: 8pt;
            text-align: center;
            border-bottom: 1px dashed #000;
            line-height: 14pt;
            padding-bottom: 1pt;
        }

        /* Baris "Nomor :" dan "Tanggal :" */
        table.meta {
            margin-top: 14pt;
            border-collapse: collapse;
        }
        table.meta td { padding: 1pt 0; }
        table.meta td.meta-label { width: 55pt; }
        table.meta td.meta-sep { width: 10pt; }

        /* Mengetahui: jabatan + nama pejabat */
        .acknowledged-by { margin-top: 22pt; }
        .official-spacer { height: 50pt; }   /* ruang tanda tangan pejabat */
        .official-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    {{-- ===== JUDUL ===== --}}
    <div class="title">SURAT PENYATAAN BEDA NAMA/IDENTITAS</div>

    {{-- ===== ISI ===== --}}
    <p>Saya yang bertanda tangan dibawah ini</p>
    <table class="data">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['nik'] }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Sesuai KTP</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] }}</td>
        </tr>
        <tr>
            <td class="label">&nbsp;</td>
            <td class="sep">&nbsp;</td>
            <td class="val">&nbsp;</td>
        </tr>
    </table>

    <p class="gap-top">Dengan ini menyatakan dengan sebenarnya bahwa terdapat perbedaan Nama/NIK/Alamat dalam</p>
    <table class="data">
        <tr>
            <td class="label">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $other_identity['name'] }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $other_identity['address'] }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="sep">:</td>
            <td class="val">{{ $other_identity['nik'] }}</td>
        </tr>
    </table>

    <p class="gap-top">Bahwa nama dan identitas yang tercantum adalah saya sendiri dan bukan orang lain.</p>

    <p class="gap-top">
        Demikian surat pernyataan ini dibuat untuk dipergunakan sebenarnya dan apabila tidak benar
        maka saya bersedia dituntut di Pengadilan sesuai perundang-undangan yang berlaku menyangkut
        penggunaan keterangan palsu
    </p>

    {{-- ===== TANDA TANGAN ===== --}}
    <div class="signature-block">
        <div class="line">Sleman, ..................</div>
        <div class="line">Yang Membuat Pernyataan</div>

        <div class="stamp-duty-box">materai<br>RP 10000</div>

        <div class="sign-name">{{ $applicant['name'] ?? '' }}&nbsp;</div>

        <table class="meta">
            <tr>
                <td class="meta-label">Nomor</td>
                <td class="meta-sep">:</td>
                <td>{{ $number ?? '' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Tanggal</td>
                <td class="meta-sep">:</td>
                <td>{{ $signature['date'] ?? '' }}</td>
            </tr>
        </table>

        <div class="acknowledged-by">
            <div class="line">Mengetahui</div>
            <div class="line">{{ $acknowledger['position'] ?? 'Jagabaya' }}</div>
            <div class="official-spacer"></div>
            @if (!empty($acknowledger['name']))
                <div class="official-name">{{ $acknowledger['name'] }}</div>
            @endif
        </div>
    </div>

</body>
</html>