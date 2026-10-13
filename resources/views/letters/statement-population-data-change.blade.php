<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan Perubahan Elemen Data Kependudukan</title>
    <style>
        {{--
            Standalone -- TIDAK extends letters.layouts.base, supaya form ini
            dijamin tidak pernah ikut menampilkan kop surat apa pun yang ada
            di layout lain. @page di sini yang mengatur ukuran & margin
            halaman untuk DomPDF (folio, portrait).

            Ukuran dipadatkan (font, padding, tinggi baris) supaya seluruh
            formulir -- termasuk paragraf pernyataan & blok tanda tangan --
            muat dalam satu halaman dan tidak terpotong di antara keduanya.
        --}}
        @page { margin: 1.5cm 2cm 1.3cm 2cm; }
        body { font-family: Times, serif; font-size: 10pt; line-height: 1.15; color: #000; margin: 0; }
        p { margin: 3pt 0; }

        /* ---------- Kotak kode formulir & huruf di kanan atas ---------- */
        table.form-code-row { width: 100%; border-collapse: collapse; margin-bottom: 5pt; }
        table.form-code-row td { padding: 0; vertical-align: top; }
        td.form-code-box {
            width: 44pt; text-align: center; font-weight: bold;
            border: 1px solid #000; padding: 2pt 0; font-size: 10pt;
        }
        td.form-code-letter { width: 12pt; text-align: center; font-size: 10pt; }
        td.form-code-spacer { width: auto; }

        /* ---------- Judul ---------- */
        .title {
            text-align: center; font-weight: bold; font-size: 11pt;
            text-decoration: underline; margin: 3pt 0 8pt 0;
        }

        /* ---------- Baris identitas (Nama/NIK/Nomor KK/Alamat) ---------- */
        table.identity { width: 100%; border-collapse: collapse; margin: 2pt 0 6pt 0; }
        table.identity td { padding: 0.5pt 0; vertical-align: bottom; }
        td.identity-lbl { width: 65pt; }
        td.identity-sep { width: 10pt; }
        td.identity-val { border-bottom: 1px dotted #000; }

        /* ---------- Tabel bergaris (grid) dipakai untuk semua tabel data ---------- */
        table.grid { width: 100%; border-collapse: collapse; margin: 3pt 0 6pt 0; page-break-inside: avoid; }
        table.grid tr { page-break-inside: avoid; }
        table.grid th, table.grid td {
            border: 1px solid #000; padding: 1.5pt 3pt; vertical-align: middle;
            font-size: 9pt;
        }
        table.grid th { font-weight: bold; text-align: center; }
        table.grid td.no-col { width: 16pt; text-align: center; }
        table.grid td.data-row { height: 13pt; }

        /* Tabel "dengan rincian KK sebagai berikut" */
        table.family col.col-no   { width: 5%; }
        table.family col.col-name { width: 28%; }
        table.family col.col-nik  { width: 24%; }
        table.family col.col-shdk { width: 18%; }
        table.family col.col-note { width: 25%; }

        /* Tabel perubahan (A. Pendidikan & Pekerjaan / B. Agama & Lainnya) */
        table.change col.col-no    { width: 4%; }
        table.change col.col-sub   { width: 16%; }
        table.change col.col-note  { width: 16%; }

        .section-title { font-weight: bold; margin: 6pt 0 2pt 0; font-size: 10pt; }

        /* Judul seksi + tabelnya dibungkus 1 blok supaya keduanya tidak
           pernah terpisah oleh page-break (judul di halaman ini, tabel di
           halaman berikutnya). */
        .section { page-break-inside: avoid; }

        p.justify { text-align: justify; }

        /* ---------- Blok tanda tangan ---------- */
        table.sign-wrap { width: 100%; border-collapse: collapse; margin-top: 6pt; }
        table.sign-wrap td { vertical-align: top; padding: 0; }
        td.sign-spacer { width: 55%; }
        td.sign-block { width: 45%; text-align: center; }
        .sign-space { height: 36pt; }
        .sign-name { border-top: 0; }

        /* ---------- Keterangan kaki formulir ---------- */
        .ket-title { font-weight: bold; margin-top: 8pt; margin-bottom: 2pt; }
        .ket-note { font-size: 8.5pt; font-style: italic; text-align: justify; }

        /* Paragraf pernyataan + blok tanda tangan dibungkus 1 wrapper supaya
           DomPDF mengusahakan keduanya tetap 1 halaman (tidak terpisah). */
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        $familyRows = 7;
        $familyMembersRows = collect($family_members ?? [])
            ->pad($familyRows, [])
            ->take($familyRows);

        $changeRows = 7;
        $educationJobRows = collect($education_job_changes ?? [])
            ->pad($changeRows, [])
            ->take($changeRows);
        $religionOtherRows = collect($religion_other_changes ?? [])
            ->pad($changeRows, [])
            ->take($changeRows);
    @endphp

    <table class="form-code-row">
        <tr>
            <td class="form-code-spacer"></td>
            <td class="form-code-box">{{ $form_code ?? 'F-1.06' }}</td>
            <td class="form-code-letter">A</td>
        </tr>
    </table>

    <div class="title">SURAT PERNYATAAN PERUBAHAN ELEMEN DATA KEPENDUDUKAN</div>

    <div class="section">
    <p>Yang bertanda tangan dibawah ini</p>
    <table class="identity">
        <tr>
            <td class="identity-lbl">Nama</td>
            <td class="identity-sep">:</td>
            <td class="identity-val">{{ $applicant['name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="identity-lbl">NIK</td>
            <td class="identity-sep">:</td>
            <td class="identity-val">{{ $applicant['nik'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="identity-lbl">Nomor KK</td>
            <td class="identity-sep">:</td>
            <td class="identity-val">{{ $applicant['kk_number'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="identity-lbl">Alamat</td>
            <td class="identity-sep">:</td>
            <td class="identity-val">{{ $applicant['address'] ?? '' }}</td>
        </tr>
    </table>
    </div>

    <div class="section">
    <p>dengan rincian KK sebagai berikut</p>
    <table class="grid family">
        <colgroup>
            <col class="col-no"><col class="col-name"><col class="col-nik">
            <col class="col-shdk"><col class="col-note">
        </colgroup>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIK</th>
            <th>SHDK</th>
            <th>Keterangan</th>
        </tr>
        @foreach ($familyMembersRows as $i => $member)
            <tr>
                <td class="no-col">{{ $i + 1 }}</td>
                <td class="data-row">{{ $member['name'] ?? '' }}</td>
                <td class="data-row">{{ $member['nik'] ?? '' }}</td>
                <td class="data-row">{{ $member['shdk'] ?? '' }}</td>
                <td class="data-row">{{ $member['note'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
    </div>

    <p>Menyatakan bahwa data elemen data kependudukan saya dan anggota keluarga saya telah berubah dengan rincian:</p>

    <div class="section">
    <div class="section-title">A. Pendidikan dan Pekerjaan</div>
    <table class="grid change">
        <colgroup>
            <col class="col-no">
            <col class="col-sub"><col class="col-sub"><col class="col-sub">
            <col class="col-sub"><col class="col-sub"><col class="col-sub">
            <col class="col-note">
        </colgroup>
        <tr>
            <th rowspan="3">No</th>
            <th colspan="6">Elemen Data</th>
            <th rowspan="3">Keterangan</th>
        </tr>
        <tr>
            <th colspan="3">Pendidikan Terakhir</th>
            <th colspan="3">Pekerjaan</th>
        </tr>
        <tr>
            <th>Semula</th><th>Menjadi</th><th>Dasar Perubahan</th>
            <th>Semula</th><th>Menjadi</th><th>Dasar Perubahan</th>
        </tr>
        @foreach ($educationJobRows as $i => $row)
            <tr>
                <td class="no-col">{{ $i + 1 }}</td>
                <td class="data-row">{{ $row['education']['before'] ?? '' }}</td>
                <td class="data-row">{{ $row['education']['after'] ?? '' }}</td>
                <td class="data-row">{{ $row['education']['basis'] ?? '' }}</td>
                <td class="data-row">{{ $row['job']['before'] ?? '' }}</td>
                <td class="data-row">{{ $row['job']['after'] ?? '' }}</td>
                <td class="data-row">{{ $row['job']['basis'] ?? '' }}</td>
                <td class="data-row">{{ $row['note'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
    </div>

    <div class="section">
    <div class="section-title">B. Agama Dan Perubahan Lainnya</div>
    <table class="grid change">
        <colgroup>
            <col class="col-no">
            <col class="col-sub"><col class="col-sub"><col class="col-sub">
            <col class="col-sub"><col class="col-sub"><col class="col-sub">
            <col class="col-note">
        </colgroup>
        <tr>
            <th rowspan="3">No</th>
            <th colspan="6">Elemen Data</th>
            <th rowspan="3">Keterangan<br><span style="font-weight:normal; font-style:italic;">tanggal nikah</span></th>
        </tr>
        <tr>
            <th colspan="3">Agama</th>
            <th colspan="3">Lainnya, {{ $other_element_label ?? '.......' }}</th>
        </tr>
        <tr>
            <th>Semula</th><th>Menjadi</th><th>Dasar Perubahan</th>
            <th>Semula</th><th>Menjadi</th><th>Dasar Perubahan</th>
        </tr>
        @foreach ($religionOtherRows as $i => $row)
            <tr>
                <td class="no-col">{{ $i + 1 }}</td>
                <td class="data-row">{{ $row['religion']['before'] ?? '' }}</td>
                <td class="data-row">{{ $row['religion']['after'] ?? '' }}</td>
                <td class="data-row">{{ $row['religion']['basis'] ?? '' }}</td>
                <td class="data-row">{{ $row['other']['before'] ?? '' }}</td>
                <td class="data-row">{{ $row['other']['after'] ?? '' }}</td>
                <td class="data-row">{{ $row['other']['basis'] ?? '' }}</td>
                <td class="data-row">{{ $row['note'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
    </div>

    <div class="no-break">
        <p class="justify">
            Terlampir kami sampaikan Fotocopi dari berkas-berkas yang terkait dengan perubahan perubahan
            elemen data tersebut. Demikian Surat pernyataan ini saya buat dengan sebenarnya, apabila
            dalam keterangan yang saya berikan terdapat hal-hal yang tidak berdasarkan keadaan yang
            sebenarnya, saya bersedia dikenakan sangsi sesuai ketentuan peraturan perundang-undangan
            yang berlaku.
        </p>

        <table class="sign-wrap">
            <tr>
                <td class="sign-spacer"></td>
                <td class="sign-block">
                    <p>{{ $signature['city'] ?? '' }}, {{ $signature['date_long'] ?? '' }}</p>
                    <p>Yang Membuat Penyataan</p>
                    <div class="sign-space"></div>
                    <p class="sign-name">( {{ ($applicant['name'] ?? '') !== '' ? $applicant['name'] : '.....................................' }} )</p>
                </td>
            </tr>
        </table>

        <div class="ket-title">Keterangan</div>
        <p class="ket-note">
            *) Perubahan lainnya ini, juga dapat digunakan untuk merubah data kependudukan yang
            diakibatkan adanya kesalahan pada saat pengisian Formulir Biodata maupun kesalahan pada
            saat Peng-entry-an biodata dimaksud.
        </p>
    </div>
</body>
</html>