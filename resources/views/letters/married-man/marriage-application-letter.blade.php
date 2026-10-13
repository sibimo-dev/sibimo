<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Permohonan Kehendak Nikah</title>
    <style>
        @page { margin: 1.8cm 2cm 1.8cm 2cm; }
        body { margin:0; font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.35; color:#000; }
        p { margin: 0; }
        .form-ref { text-align:right; font-size: 8.5pt; line-height: 1.2; }
        .dateline { text-align:right; margin-top: 6pt; }
        .subject { margin-top: 14pt; font-weight:bold; }
        .recipient { margin-top: 10pt; }
        p.gap-top { margin-top: 10pt; }

        table.data { width:100%; border-collapse:collapse; margin-top: 4pt; }
        table.data td { padding: 1.5pt 0; vertical-align:top; }
        td.lbl { width: 130pt; }
        td.sep { width: 12pt; }
        td.val { padding-left: 3pt; height: 13pt; }

        ol.docs { margin: 6pt 0 0 18pt; padding: 0; }
        ol.docs li { margin-bottom: 2pt; }
        .time-label { display: inline-block; margin-left: 90pt;}

        table.footer { width:100%; border-collapse:collapse; margin-top: 20pt; }
        table.footer td { width:50%; vertical-align:top; }
        .footer-right { text-align:right; }
        .footer-space { height: 30pt; }
    </style>
</head>
<body>

    <div class="form-ref">
        Lampiran VI Keputusan Dirjen Bimas Islam<br>
        Nomor 473 Tahun 2020 tentang<br>
        Petunjuk Teknis Pelaksanaan Pencatatan Nikah<br><br>
        Model N2
    </div>

    <div class="dateline">
        {{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}
    </div>

    <div class="subject">Perihal : Permohonan Kehendak Nikah</div>

    <div class="recipient">
        Kepada Yth.<br>
        <strong>Kepala KUA Kecamatan Ngemplak</strong><br>
        <strong>Di Ngemplak</strong>
    </div>

    <p class="gap-top">Assalamu'alaikum wr. wb.</p>

    <p class="gap-top">
        Dengan hormat kami mengajukan permohonan kehendak nikah untuk atas nama:
    </p>

    <table class="data">
        <tr>
            <td class="lbl">Calon suami</td>
            <td class="sep">:</td>
            <td class="val">{{ $groom_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Calon istri</td>
            <td class="sep">:</td>
            <td class="val">{{ $bride_name ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Hari/tanggal/jam</td>
            <td class="sep">:</td>
            <td class="val">
                {{ $ceremony['date_time'] ?? trim(($ceremony['day'] ?? '') . ', ' . ($ceremony['date'] ?? ''), ', ') }}
                <span class="time-label">Jam : {{ $ceremony['time'] ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td class="lbl">Tempat akad nikah</td>
            <td class="sep">:</td>
            <td class="val">{{ $ceremony['place'] ?? '' }}</td>
        </tr>
    </table>

    <p class="gap-top">
        Bersama ini kami sampaikan surat-surat yang diperlukan untuk diperiksa sebagai:
    </p>

    <ol class="docs">
        <li>Surat Pengantar nikah dari Desa/Kelurahan;</li>
        <li>Persetujuan Calon Mempelai;</li>
        <li>Fotocopy KTP;</li>
        <li>Fotocopy akte kelahiran;</li>
        <li>Fotocopy Kartu Keluarga;</li>
        <li>Pas foto 2x3 = 5 lembar, dan 4x6 = 1 lembar, berlatar belakang BIRU;</li>
        <li>Surat Keterangan Wali Nikah;</li>
        <li>&nbsp;</li>
    </ol>

    <p class="gap-top">
        Demikian permohonan ini kami sampaikan, kiranya dapat diperiksa, dihadiri dan dicatat sesuai
        dengan ketentuan peraturan perundang-undangan.
    </p>

    <table class="footer">
        <tr>
            <td>
                <div>Diterima tanggal: ________________</div>
                <div style="margin-top: 8pt;">Yang menerima,</div>
                <div>Kepala KUA/Penghulu</div>
            </td>
            <td class="footer-right">
                <div>Wassalam,</div>
                <div>Pemohon</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="footer-space">&nbsp;</div>
            </td>
            <td class="footer-right">
                <div class="footer-space">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td class="footer-right">{{ $applicant_name ?? '' }}</td>
        </tr>
    </table>

</body>
</html>