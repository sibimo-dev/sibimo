@php
    $occurrence = $populationOccurrence ?? [];
    $person = $occurrence['applicant'] ?? [];
    $selected = $occurrence['selected'] ?? [];
    $attachedDocuments = $occurrence['attached_documents'] ?? [];
    $applicationDate = $occurrence['application_date'] ?? null;

    $isSelected = fn (?string $key): bool => $key !== null && ! empty($selected[$key]);
    $isAttached = fn (string $key): bool => ! empty($attachedDocuments[$key]);

    // ---- "Jenis Permohonan" table: one entry per row, [code, label, key|null] or null = empty cell.
    // Row order follows the original form (14 rows). key = selectable via `application_types`.
    $familyCardColumn = [
        ['A', 'BARU', null],
        ['1', 'Membentuk Keluarga baru', 'family_card_new_family'],
        ['2', 'Penggantian Kepala Keluarga', 'family_card_head_change'],
        ['3', 'Pisah KK', 'family_card_split'],
        ['4', 'Pindah Datang', 'family_card_move_in'],
        ['5', 'WNI LN Karena Pindah', 'family_card_citizen_abroad_return'],
        ['6', 'Rentan Adminduk', 'family_card_vulnerable_group'],
        ['B', 'PERUBAHAN DATA', null],
        ['1', 'Menumpang dalam KK', 'family_card_join_existing'],
        ['2', 'Peristiwa Penting', 'family_card_important_occurrence'],
        ['3', 'Perubahan elemen data yang tercantum dalam KK', 'family_card_data_element_change'],
        ['C', 'HILANG/RUSAK', null],
        ['1', 'Hilang', 'family_card_lost'],
        ['2', 'Rusak', 'family_card_damaged'],
    ];

    $idCardColumn = [
        ['A', 'BARU', 'id_card_new'],
        null,
        ['B', 'PINDAH DATANG', 'id_card_move_in'],
        null,
        ['C', 'HILANG/RUSAK', null],
        ['1.', 'Hilang', 'id_card_lost'],
        ['2.', 'Rusak', 'id_card_damaged'],
        null,
        ['D', 'PERPANJANGAN ITAP', 'id_card_itap_extension'],
        null,
        ['E', 'PERUBAHAN STATUS KEWARGANEGARAAN', 'id_card_citizenship_change'],
        ['F', 'LUAR DOMISILI', 'id_card_out_of_domicile'],
        null,
        ['G', 'TRASNMIGRASI', 'id_card_transmigration'],
    ];

    $childCardColumn = [
        ['A', 'BARU', 'child_card_new'],
        null,
        ['B', 'HILANG/RUSAK', null],
        ['1.', 'Hilang', 'child_card_lost'],
        ['2.', 'Rusak', 'child_card_damaged'],
        null,
        ['C', 'Perpanjangan ITAP', 'child_card_itap_extension'],
        null,
        ['D', 'Lainya', 'child_card_other'],
        null, null, null, null, null,
    ];

    // Column IV only has 5 own rows; from row 6 on it is one merged cell holding the notes.
    $dataChangeColumn = [
        ['A', 'KK', 'data_change_family_card'],
        null,
        ['B', 'KTP-EL', 'data_change_id_card'],
        null,
        ['C', 'KIA', 'data_change_child_card'],
    ];

    // Isi sel kode: huruf/angka di tengah; kalau dipilih, tanda "V" muncul di kiri sel.
    $codeContent = function (?string $code, bool $checked): string {
        $code = e($code ?? '');
        if (! $checked) {
            return $code;
        }

        return '<table style="width: 100%;"><tr>'
            . '<td style="width: 7pt; text-align: left;">V</td>'
            . '<td style="text-align: center;">' . $code . '</td>'
            . '<td style="width: 7pt;"></td>'
            . '</tr></table>';
    };

    // Row heights in pt (measured from the original scan).
    $rowHeights = [18, 18, 20, 18, 17, 21, 14, 24, 22, 28, 46, 18, 18, 18];

    // Groups I, II, III: [column data, inside-mark?]. Group I keeps its "V" mark outside the table.
    $requestGroups = [
        ['cells' => $familyCardColumn, 'outside' => true],
        ['cells' => $idCardColumn, 'outside' => false],
        ['cells' => $childCardColumn, 'outside' => false],
    ];

    // ---- "Persyaratan yang dilampirkan": [left key, left label, right key, right label] per row.
    $documentRows = [
        ['old_family_card', 'KK Lama/Rusak',
         'occurrence_evidence', "Surat Keterangan/Bukti Perubahan\nPeristiwa Kependudukan Dan Peristiwa Penting"],
        ['marriage_certificate', 'Buku Nikah/Kutipan Akta Perkawinan',
         'unregistered_marriage_statement', 'SPTJM perkawinan/perceraian belum tercatat'],
        ['divorce_certificate', 'Kutipan Akta Perceraian',
         'death_certificate', 'Akta Kematian'],
        ['move_out_certificate', 'Surat Keterangan Pindah',
         'loss_damage_cause_statement', 'Surat Pernyataan penyebab terjadinya Hilang atau rusak'],
        ['move_abroad_certificate', 'Surat Keterangan Pindah Luar Negeri',
         'foreign_mission_move_certificate', 'Surat Keterangan Pindah dari Perwakilan RI'],
        ['damaged_id_card', 'KTP -El Rusak',
         'family_acceptance_statement', 'Surat pernyataan bersedia menerima sebagai anggota keluarga'],
        ['travel_document', 'Dokumen Perjalanan',
         'child_custody_power_of_attorney', 'Surat kuasa pengasuhan anak dari orang tua/wali'],
        ['police_loss_report', 'Surat Keterangan Hilang dari Kepolisian',
         'residence_permit_card', 'Kartu Izin Tinggal Tetap'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Formulir Pendaftaran Peristiwa Kependudukan</title>
    <style>
        @page {
            margin: 58pt 74pt 30pt 74pt;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.15;
            color: #000000;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        td, th {
            padding: 0;
            vertical-align: top;
            font-weight: bold;
        }

        /* ---------- form code box (top right) ---------- */
        .top-table {
            width: 100%;
        }

        .form-code-box {
            width: 100pt;
            height: 12pt;
            border: 1pt solid #000000;
            border-right: 2pt solid #000000;
            border-bottom: 2pt solid #000000;
            text-align: center;
            vertical-align: middle;
            font-size: 6.5pt;
        }

        .top-gap {
            width: 14pt;
        }

        /* ---------- title ---------- */
        .title {
            margin: 3pt 0 14pt 0;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
        }

        /* ---------- section headings ---------- */
        .section-table {
            width: 100%;
        }

        .section-table td {
            font-size: 7.5pt;
            text-transform: uppercase;
        }

        .section-table .h-num {
            width: 24pt;
            padding-left: 15pt;
            text-align: left;
        }

        /* ---------- I. applicant data ---------- */
        .fields {
            width: 458pt;
            margin-top: 2pt;
        }

        .fields td {
            font-size: 7.5pt;
            text-transform: uppercase;
        }

        .f-pad {
            width: 39pt;
        }

        .f-number {
            width: 16pt;
            vertical-align: bottom;
            padding-bottom: 2pt;
        }

        .f-label {
            width: 177pt;
            vertical-align: bottom;
            padding-bottom: 2pt;
        }

        .f-colon {
            width: 7pt;
            vertical-align: bottom;
            padding-bottom: 2pt;
        }

        .f-box {
            width: 219pt;
            border: 0.8pt solid #000000;
            padding: 0 5pt;
            font-size: 8pt;
            vertical-align: bottom;
            padding-bottom: 2pt;
        }

        /* ---------- II. request table ---------- */
        .request {
            width: 448pt;
            margin-left: 15pt;
        }
        .w1m { width: 11pt; }
        .w1c { width: 26.5pt; }
        .w1l { width: 90pt; }
        .w2c { width: 26.5pt; }
        .w2l { width: 76.3pt; }
        .w3c { width: 26.5pt; }
        .w3l { width: 76.3pt; }
        .w4c { width: 26.5pt; }
        .w4l { width: 76.3pt; }

        .request td {
            font-size: 7pt;
            line-height: 1.15;
        }

        .request .head td {
            height: 21pt;
            background-color: #ffffff;
        }

        .mark-outside {
            border: 0;
            text-align: center;
            padding-top: 2pt;
        }

        .mark {
            border-top: 0.8pt solid #000000;
            border-bottom: 0.8pt solid #000000;
            border-left: 0.8pt solid #000000;
            text-align: center;
            padding-top: 2pt;
        }

        .code {
            border-top: 0.8pt solid #000000;
            border-bottom: 0.8pt solid #000000;
            border-right: 0.8pt solid #000000;
            text-align: center;
            padding-top: 2pt;
        }

        .codebox {
            border: 0.8pt solid #000000;
            text-align: center;
            padding-top: 2pt;
        }

        .code-first {
            border-left: 0.8pt solid #000000;
        }

        .label {
            border-top: 0.8pt solid #000000;
            border-bottom: 0.8pt solid #000000;
            border-right: 0.8pt solid #000000;
            padding: 2pt 1pt 1pt 2pt;
        }

        .head .codebox,
        .head .code,
        .head .label {
            padding-top: 2pt;
        }

        .head .mark-outside {
            border: 0;
        }

        .notes p {
            margin: 0;
        }

        /* ---------- III. attached documents ---------- */
        .documents {
            width: 464pt;
        }

        .documents td {
            font-size: 6pt;
            line-height: 1.2;
        }

        .d-pad {
            width: 34pt;
        }
        .d-circle,
        .d-circle-right {
            width: 18pt;
            padding-right: 7pt;
        }

        .d-label-left {
            width: 191pt;
        }

        .d-label-right {
            width: 189pt;
        }

        .circle {
            width: 16pt;
            height: 13pt;
            border: 1.2pt solid #000000;
            border-radius: 50%;
            background-color: #ffffff;
        }

        .circle-on {
            background-color: #000000;
        }

        /* ---------- footer date ---------- */
        .date-table {
            margin-top: 12pt;
        }

        .date-table td {
            font-size: 7.5pt;
        }
    </style>
</head>
<body>

    {{-- Form code --}}
    <table class="top-table">
        <tr>
            <td></td>
            <td class="form-code-box">{{ $form_code ?: 'F-1.02' }}</td>
            <td class="top-gap"></td>
        </tr>
    </table>

    <div class="title">Formulir Pendaftaran Peristiwa Kependudukan</div>

    {{-- I. APPLICANT DATA --}}
    <table class="section-table">
        <tr>
            <td class="h-num">I</td>
            <td>DATA PEMOHON</td>
        </tr>
    </table>

    <table class="fields">
        <tr>
            <td class="f-pad"></td>
            <td class="f-number">1.</td>
            <td class="f-label">NAMA LENGKAP</td>
            <td class="f-colon">:</td>
            <td class="f-box" style="height: 18pt;">{{ $person['name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="f-pad"></td>
            <td class="f-number"></td>
            <td class="f-label"></td>
            <td class="f-colon"></td>
            <td class="f-box" style="height: 18pt;"></td>
        </tr>
        <tr>
            <td class="f-pad"></td>
            <td class="f-number">2.</td>
            <td class="f-label">NOMOR INDUK KEPENDUDUKAN</td>
            <td class="f-colon">:</td>
            <td class="f-box" style="height: 17pt;">{{ $person['nik'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="f-pad"></td>
            <td class="f-number">3.</td>
            <td class="f-label">NOMOR KARTU KELUARGA</td>
            <td class="f-colon">:</td>
            <td class="f-box" style="height: 17pt;">{{ $person['family_card_number'] ?? '' }}</td>
        </tr>
    </table>

    {{-- II. REQUEST TYPE --}}
    <table class="section-table" style="margin-top: 22pt;">
        <tr>
            <td class="h-num">II</td>
            <td>JENIS PERMOHONAN</td>
        </tr>
    </table>

    <table class="request" style="margin-top: 8pt;">
        <tr class="head">
            <td class="mark-outside w1m"></td>
            <td class="code code-first w1c">I</td>
            <td class="label w1l">KARTU KELUARGA</td>

            <td class="codebox w2c">II</td>
            <td class="label w2l">KTP-EL</td>

            <td class="codebox w3c">III</td>
            <td class="label w3l">KARTU IDENTITAS ANAK/KIA</td>

            <td class="codebox w4c">IV</td>
            <td class="label w4l">PERUBAHAN DATA</td>
        </tr>

        @foreach ($rowHeights as $index => $rowHeight)
            @php $cellHeight = 'height: ' . ($rowHeight - 3) . 'pt;'; @endphp
            <tr>
                @foreach ($requestGroups as $groupIndex => $group)
                    @php
                        $n = $groupIndex + 1;
                        $cell = $group['cells'][$index] ?? null;
                        $checked = $cell ? $isSelected($cell[2]) : false;
                    @endphp
                    @if ($group['outside'])
                        <td class="mark-outside w{{ $n }}m" style="{{ $cellHeight }}">{{ $checked ? 'V' : '' }}</td>
                        <td class="code code-first w{{ $n }}c" style="{{ $cellHeight }}">{{ $cell[0] ?? '' }}</td>
                    @else
                        <td class="codebox w{{ $n }}c" style="{{ $cellHeight }}">{!! $codeContent($cell[0] ?? '', $checked) !!}</td>
                    @endif
                    <td class="label w{{ $n }}l" style="{{ $cellHeight }}">{{ $cell[1] ?? '' }}</td>
                @endforeach

                {{-- Column IV --}}
                @if ($index < count($dataChangeColumn))
                    @php
                        $cell = $dataChangeColumn[$index];
                        $checked = $cell ? $isSelected($cell[2]) : false;
                    @endphp
                    <td class="codebox w4c" style="{{ $cellHeight }}">{!! $codeContent($cell[0] ?? '', $checked) !!}</td>
                    <td class="label w4l" style="{{ $cellHeight }}">{{ $cell[1] ?? '' }}</td>
                @elseif ($index === count($dataChangeColumn))
                    @php $mergedRows = count($rowHeights) - count($dataChangeColumn); @endphp
                    <td class="codebox w4c" rowspan="{{ $mergedRows }}"></td>
                    <td class="label w4l notes" rowspan="{{ $mergedRows }}">
                        <p>1) Formulir Perubahan data dan</p>
                        <div style="height: 71pt;"></div>
                        <p>2) Bukti Perubahan Data</p>
                    </td>
                @endif
            </tr>
        @endforeach
    </table>

    {{-- III. ATTACHED REQUIREMENTS --}}
    <table class="section-table" style="margin-top: 13pt;">
        <tr>
            <td class="h-num">III.</td>
            <td>PERSYARATAN YANG DILAMPIRKAN</td>
        </tr>
    </table>

    <table class="documents" style="margin-top: 12pt;">
        @foreach ($documentRows as $rowIndex => $documentRow)
            @php
                // Semua baris: bulatan & tulisan sama-sama rata tengah (vertical-align: middle)
                // supaya bulatan selalu lurus dengan tulisan. Label multi-baris ("\n") ditulis
                // baris pertamanya di sini, sisanya di baris lanjutan di bawah.
                $leftLines = explode("\n", $documentRow[1]);
                $rightLines = explode("\n", $documentRow[3]);
                $hasContinuation = count($leftLines) > 1 || count($rightLines) > 1;
            @endphp
            <tr>
                <td class="d-pad" style="height: 18pt;"></td>
                <td class="d-circle" style="vertical-align: middle;">
                    <div class="circle {{ $isAttached($documentRow[0]) ? 'circle-on' : '' }}"></div>
                </td>
                <td class="d-label-left" style="vertical-align: middle;">{{ $leftLines[0] }}</td>
                <td class="d-circle-right" style="vertical-align: middle;">
                    <div class="circle {{ $isAttached($documentRow[2]) ? 'circle-on' : '' }}"></div>
                </td>
                <td class="d-label-right" style="vertical-align: middle;">{{ $rightLines[0] }}</td>
            </tr>
            @if ($hasContinuation)
                <tr>
                    <td class="d-pad" style="height: 18pt;"></td>
                    <td class="d-circle"></td>
                    <td class="d-label-left" style="vertical-align: middle;">{!! nl2br(e(implode("\n", array_slice($leftLines, 1)))) !!}</td>
                    <td class="d-circle-right"></td>
                    <td class="d-label-right" style="vertical-align: middle;">{!! nl2br(e(implode("\n", array_slice($rightLines, 1)))) !!}</td>
                </tr>
            @endif
        @endforeach
    </table>

    {{-- Footer: place & date --}}
    <table class="date-table">
        <tr>
            <td style="width: 252pt;"></td>
            <td style="width: 49pt;">Sleman,</td>
            <td>{{ $applicationDate ?: '..........................' }}</td>
        </tr>
    </table>

</body>
</html>