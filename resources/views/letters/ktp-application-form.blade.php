<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Formulir Permohonan KTP</title>
    <style>
        @page { margin: 1cm 1.3cm 1cm 1.3cm; }

        body {
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.15;
            color: #000;
        }

        .form-code {
            border: 1px solid #000;
            width: 56pt;
            text-align: center;
            font-weight: bold;
            padding: 3pt 0;
            float: right;
        }
        .clear { clear: both; }

        .form-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            margin-top: 4pt;
        }

        /* Region grid: label + N small code boxes + 1 long value box */
        table.region-grid { width: 100%; border-collapse: collapse; margin-top: 8pt; }
        table.region-grid td { padding: 0; vertical-align: middle; }
        table.region-grid td.region-label { width: 150pt; font-size: 9.5pt; padding-right: 0; font-weight: bold; }
        table.region-grid td.region-sep { width: 10pt; }
        table.region-grid td.region-code-cell { width: 130pt; }
        table.region-grid table.region-code { border-collapse: collapse; }
        table.region-grid table.region-code td {
            border: 1px solid #000; width: 17pt; height: 12pt;
            text-align: center; font-weight: bold; font-size: 9pt;
        }
        table.region-grid td.region-value { border: 1px solid #000; padding-left: 4pt; height: 13pt; }

        /* KTP request type row: each option = small checkbox + label box,
           joined together; options are spaced apart. */
        table.option-row { width: 100%; border-collapse: separate; border-spacing: 14pt 0; margin-top: 10pt; }
        table.option-row td { padding: 0; vertical-align: middle; font-size: 9.5pt; }
        table.option-row td.option-label { font-weight: bold; }
        table.option-group { border-collapse: collapse; }
        table.option-group td.option-check {
            border: 1px solid #000; width: 13pt; height: 13pt;
            text-align: center; font-weight: bold; font-size: 9pt;
        }
        table.option-group td.option-text { border: 1px solid #000; border-left: none; padding: 2pt 10pt; }

        /* Applicant data table (name / KK no. / NIK / address) - a single flat table,
           no nested tables, so borders are not doubled. */
        table.form-data { width: 100%; border-collapse: collapse; margin-top: 8pt; }
        table.form-data td {
            border: 1px solid #000; font-size: 9pt; vertical-align: middle; height: 16pt;
        }
        table.form-data td.label {
            font-weight: bold; text-align: left; padding: 2pt 6pt;
            white-space: nowrap; font-size: 8.7pt;
        }
        table.form-data td.char-cell { text-align: center; padding: 0; width: 20pt; }
        table.form-data td.free-text { text-align: left; padding-left: 4pt; }

        /* RT / RW */
        table.rt-rw { width: auto; border-collapse: separate; border-spacing: 0; margin-top: 8pt; }
        table.rt-rw td { vertical-align: middle; font-size: 9.5pt; padding: 0 4pt; }
        table.rt-rw td.rt-rw-label { font-weight: bold; }
        table.digit-box { border-collapse: collapse; display: inline-table; }
        table.digit-box td { border: 1px solid #000; width: 14pt; height: 13pt; text-align: center; }

        /* Photo / 3 small boxes / signature specimen (left) + signature block (right).
           No rowspan, so the tall signature block cannot stretch the left rows. */
        table.photo-section { width: 100%; border-collapse: collapse; margin-top: 14pt; }
        table.photo-section td.section-left  { width: 58%; vertical-align: top; padding: 0; }
        table.photo-section td.section-right { width: 42%; vertical-align: top; padding: 4pt 0 0 10pt; }

        table.photo-table { width: 100%; border-collapse: collapse; }
        table.photo-table td { border: 1px solid #000; padding: 2pt 4pt; vertical-align: top; }
        table.photo-table tr.header-row td { height: 14pt; }

        table.signature-inner { width: 100%; border-collapse: collapse; }
        table.signature-inner td { border: none; padding: 0; font-size: 9.5pt; }
    </style>
</head>
<body>

    <div class="form-code">{{ $kodeForm ?? '' }}</div>
    <div class="clear"></div>

    @php
        $type = $application['type'] ?? null;
        $totalChars = 20;
    @endphp

    <table class="form-data" style="margin-top:0;">
        <tr>
            <td colspan="2" class="free-text" style="height:auto; padding:4pt;">
                <strong>Perhatian</strong><br>
                1. Harap dicetak dengan huruf cetak dan menggunakan tinta hitam<br>
                2. Untuk tanda silang harap memberi tanda silang (X) pada kotak pilihan<br>
                3. Setelah formulir ini diisi dan ditandatangani harap diserahkan kembali ke Desa
            </td>
        </tr>
    </table>

    <table class="region-grid">
        <tr>
            <td class="region-label">PEMERINTAH PROVINSI</td>
            <td class="region-sep">:</td>
            <td class="region-code-cell">
                <table class="region-code"><tr>
                    @foreach ($region['province_code'] as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td class="region-value">{{ $region['province_name'] }}&nbsp;</td>
        </tr>
        <tr>
            <td class="region-label">PEMERINTAH KABUPATEN</td>
            <td class="region-sep">:</td>
            <td class="region-code-cell">
                <table class="region-code"><tr>
                    @foreach ($region['regency_code'] as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td class="region-value">{{ $region['regency_name'] }}&nbsp;</td>
        </tr>
        <tr>
            <td class="region-label">KECAMATAN</td>
            <td class="region-sep">:</td>
            <td class="region-code-cell">
                <table class="region-code"><tr>
                    @foreach ($region['district_code'] as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td class="region-value">{{ $region['district_name'] }}&nbsp;</td>
        </tr>
        <tr>
            <td class="region-label">DESA</td>
            <td class="region-sep">:</td>
            <td class="region-code-cell">
                <table class="region-code"><tr>
                    @foreach (array_slice($region['village_code'], -2) as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td class="region-value">{{ $region['village_name'] }}&nbsp;</td>
        </tr>
    </table>

    <table class="option-row">
        <tr>
            <td class="option-label" style="width:100pt;">PERMOHONAN KTP</td>
            <td>
                <table class="option-group"><tr>
                    <td class="option-check">{{ $type === 'new' ? 'X' : '' }}</td>
                    <td class="option-text">Baru</td>
                </tr></table>
            </td>
            <td>
                <table class="option-group"><tr>
                    <td class="option-check">{{ $type === 'extension' ? 'X' : '' }}</td>
                    <td class="option-text">Perpanjangan</td>
                </tr></table>
            </td>
            <td>
                <table class="option-group"><tr>
                    <td class="option-check">{{ $type === 'replacement' ? 'X' : '' }}</td>
                    <td class="option-text">Penggantian</td>
                </tr></table>
            </td>
        </tr>
    </table>

    <table class="form-data">
        <tr>
            <td class="label">Nama Lengkap</td>
            @include('letters.char-boxes', ['value' => $applicant['name'], 'length' => $totalChars])
        </tr>
        <tr>
            <td class="label">No KK</td>
            @include('letters.char-boxes', ['value' => $applicant['kk_number'], 'length' => $totalChars])
        </tr>
        <tr>
            <td class="label">NIK</td>
            @include('letters.char-boxes', ['value' => $applicant['nik'], 'length' => $totalChars])
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="free-text" colspan="{{ $totalChars }}">{{ $applicant['address'] }}</td>
        </tr>
    </table>

    <table class="rt-rw">
        <tr>
            <td class="rt-rw-label">RT</td>
            <td>
                <table class="digit-box"><tr>
                    @foreach (array_pad(str_split((string) $applicant['rt']), 2, '') as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td style="width:20pt;"></td>
            <td class="rt-rw-label">RW</td>
            <td>
                <table class="digit-box"><tr>
                    @foreach (array_pad(str_split((string) $applicant['rw']), 5, '') as $digit)
                        <td>{{ $digit }}</td>
                    @endforeach
                </tr></table>
            </td>
        </tr>
    </table>

    <table class="photo-section">
        <tr>
            <td class="section-left">
                <table class="photo-table">
                    <tr class="header-row">
                        <td style="width:35%;">Pas Poto (3X4)</td>
                        <td style="width:4.6%;"></td>
                        <td style="width:4.6%;"></td>
                        <td style="width:4.6%;"></td>
                        <td style="width:51.2%;">Spesimen Tanda Tangan</td>
                    </tr>
                    <tr>
                        <td><div style="height:46pt;"></div></td>
                        <td colspan="3"></td>
                        <td><div style="height:46pt;"></div></td>
                    </tr>
                </table>
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48.8%;"></td>
                        <td style="width:51.2%; font-size:8.5pt; padding-top:2pt;">Ket : Cap Jempol/Tanda Tangan</td>
                    </tr>
                </table>
            </td>

            <td class="section-right">
                <table class="signature-inner">
                    <tr>
                        <td></td>
                        <td style="text-align:right;">{{ $signature['city'] }}, {{ $signature['date'] }}</td>
                    </tr>
                    <tr>
                        <td style="text-align:left; padding-top:2pt;">Dukuh</td>
                        <td style="text-align:right; padding-top:2pt;">Pemohon</td>
                    </tr>
                </table>
                {{-- Ruang tanda tangan dikosongkan; nama diisi manual / dari SIBIMO publik nanti. --}}
                <div style="height:50pt;"></div>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-top:14pt;">
        <tr>
            <td style="width:58%;"></td>
            <td style="width:42%; padding-left:10pt;">
                <table class="signature-inner">
                    <tr>
                        <td style="text-align:left;">
                            <div style="height:40pt;"></div>
                            Camat
                        </td>
                        <td style="text-align:right;">
                            <div style="height:40pt;"></div>
                            Mengetahui Lurah
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>