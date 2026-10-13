<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    @php
        $marriageTitles = [
            'registration-form' => 'Data Isian Pendaftaran Nikah',
            'n1' => 'Pengantar Nikah (N1)',
            'n2' => 'Permohonan Kehendak Nikah (N2)',
            'n4' => 'Persetujuan Calon Pengantin (N4)',
            'n5' => 'Surat Izin Orang Tua (N5)',
            'n6' => 'Surat Keterangan Kematian (N6)',
            'guardian-statement' => 'Surat Keterangan Wali Nikah',
            'judge-guardian' => 'Surat Keterangan Wali Hakim',
            'health-referral' => 'Surat Pengantar Tes Kesehatan Perempuan',
            'unmarried-statement' => 'Surat Pernyataan Belum Menikah Lagi',
            'unmarried-certificate' => 'Surat Keterangan Belum Kawin',
            'numpang-nikah' => 'Surat Keterangan Numpang Nikah',
        ];
        $documentTitle = $marriageTitles[$letter ?? ''] ?? 'Surat Pernikahan';
    @endphp
    <title>{{ $documentTitle }}</title>
    <style>
        @page { margin: 1.3cm 2cm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5pt; line-height: 1.15; color: #000; margin: 0; }

        p { margin: 5pt 0; }
        .bold { font-weight: bold; }
        .u { text-decoration: underline; }
        .judul { text-align: center; font-weight: bold; margin-top: 8pt; }
        .nomor { text-align: center; margin-bottom: 10pt; }
        .lampiran { text-align: right; font-size: 9pt; line-height: 1.2; }
        .model { text-align: right; font-weight: bold; margin-top: 6pt; }
        .section-title { margin-bottom: 1pt; }
        .page-break { page-break-before: always; }
        .section { margin: 0 0 7pt 0; }
        .section-title { font-size: 9pt; margin-bottom: 1pt; }

        table.dbrows { border-collapse: collapse; margin-left: 0; }
        table.dbrows td { padding: 0 0 0.5pt 0; vertical-align: top; font-size: 8.5pt; line-height: 1.2; }
        table.dbrows td.lbl { width: 148pt; }
        table.dbrows td.sep { width: 10pt; }

        table.office { margin: 8pt 0 10pt; }
        table.office td { padding: 0; }
        table.office td.lbl { width: 175pt; white-space: nowrap; }
        
        table.rows { border-collapse: collapse; margin-left: 8pt; }
        table.rows td { padding: 0 0 1pt 0; vertical-align: top; }
        td.tag { width: 16pt; font-weight: bold; }
        td.no  { width: 18pt; }
        td.lbl { width: 165pt; }
        td.sep { width: 10pt; }

        table.sign { width: 100%; margin-top: 14pt; page-break-inside: avoid; }
        td.sign-l, td.sign-r { width: 50%; vertical-align: top; }
        td.sign-l { text-align: left; }
        td.sign-r { text-align: center; }
        td.sign-space { height: 44pt; }
        .blank { display: inline-block; min-width: 120pt; border-bottom: 1px solid #000; }

        @stack('styles')
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
