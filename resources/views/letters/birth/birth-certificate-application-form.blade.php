@extends('letters.layouts.form')

@section('title', 'Permohonan Akta Kelahiran')


@section('extra_css')
    body { font-family: "Times New Roman", Times, serif; }
    .bf { font-size: 10pt; line-height: 1.15; }
    .sec-title { font-weight: bold; margin-top: 5pt; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding: 1.7pt 0; vertical-align: top; }
    table.rows td.r-no { width: 14pt; padding-left: 10pt; }
    table.rows.flat td.r-no { padding-left: 0; }
    table.rows td.r-sep { width: 10pt; }
    table.rows td.r-val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 11pt; }

    table.head { width: 100%; border-collapse: collapse; margin-top: 18pt; margin-bottom: 6pt; page-break-inside: avoid; }
    table.head td { vertical-align: middle; }
    td.head-spacer { width: 130pt; }
    td.head-title { text-align: center; font-weight: bold; font-size: 12pt; }
    td.head-no { width: 130pt; }
    .no-box { border: 1px solid #000; height: 34pt; padding: 3pt 5pt; font-weight: bold; font-size: 8pt; }
    table.doc-list { width: 100%; border-collapse: collapse; margin-top: 3pt; }
    table.doc-list td { vertical-align: middle; height: 15pt; }
    table.doc-list td.doc-box { width: 22pt; border: 1px solid #000; text-align: center; font-weight: bold; }
    table.doc-list td.doc-label { padding-left: 8pt; }
    .dash-line { display: inline-block; border-bottom: 1px dashed #000; }
@endsection

@section('content')
@php
    $c = $birth['child'];
    $m = $birth['mother'];
    $f = $birth['father'];
    $r = $birth['reporter'];
    $mar = $birth['marriage'];

    // key => label. Key dicocokkan dengan $birth['documents'] (dokumen yang tercentang).
    $documents = [
        'medical_birth_attestation'      => 'Surat Keterangan lahir dari dokter/bidan/penolong kelahiran',
        'village_birth_attestation'      => 'Surat keterangan kelahiran',
        'marriage_certificate_copy'      => 'Fotokopi buku nikah/ kutipan akta perkawinan orang tua (dilegalisir)',
        'family_card_copy'               => 'Fotokopi Kartu Keluarga (KK) orangtua/wali',
        'parents_id_copy'                => 'Fotokopi KTP-el orangtua/wali/pelapor',
        'witnesses_id_copy'              => 'Fotokopi KTP-el 2(dua) orang saksi',
        'power_of_attorney'              => 'Surat Kuasa dan fotokopi KTP-el penerima kuasa',
        'passport_copy'                  => 'Fotokopi paspor bagi WNI bukan penduduk dan orang asing',
        'temporary_residence_certificate' => 'Fotokopi Surat Keterangan Tempat Tinggal (SKKT) orang tua bagi pemegang ITAS',
        'office_head_decree'             => 'Surat Keputusan Kepala Dinas Kependudukan Dan Pencatatan Sipil',
        'sptjm_birth_data'               => 'SPTJM Kebenaran Data Kelahiran',
        'sptjm_spouse_status'            => 'SPTJM Kebenaran Sebagai Pasangan Suami Istri',
    ];

    // ===== Pengganti partial 'rows'.
    $renderRows = function (array $rows, int $lw = 140, bool $flat = false) {
        $n = 1;
        $cls = 'rows' . ($flat ? ' flat' : '');
        $html = '<table class="' . $cls . '">';
        foreach ($rows as $row) {
            $bare = ! empty($row['cont']);
            $numbered = ! $bare && empty($row['nonum']);
            $no = $numbered ? ($n++) . '.' : '';
            $sep = $bare ? '' : ':';
            $label = e($row['label'] ?? '');
            $value = e($row['value'] ?? '');
            $html .= '<tr>'
                . '<td class="r-no">' . $no . '</td>'
                . '<td class="r-lbl" style="width: ' . $lw . 'pt;">' . $label . '</td>'
                . '<td class="r-sep">' . $sep . '</td>'
                . '<td class="r-val">' . $value . '</td>'
                . '</tr>';
        }
        return $html . '</table>';
    };
@endphp

<div class="bf">

    <table class="head">
        <tr>
            <td class="head-spacer"></td>
            <td class="head-title">PERMOHONAN AKTA KELAHIRAN</td>
            <td class="head-no"><div class="no-box">NO. {{ $birth['application_number'] }}</div></td>
        </tr>
    </table>

    <div class="sec-title">A.&nbsp;&nbsp;DATA KELUARGA</div>
    {!! $renderRows([
        ['label' => 'No. KK', 'value' => $birth['family_card_number']],
        ['label' => 'Nama Kepala Keluarga', 'value' => $birth['head_of_family_name']],
    ], 160) !!}

    <div class="sec-title">B.&nbsp;&nbsp;DATA ANAK</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $c['nik']],
        ['label' => 'Nama Lengkap', 'value' => $c['name']],
        ['label' => 'Tempat Dan Tanggal Lahir', 'value' => collect([$c['birth_place'], $c['birth_date']])->filter()->implode(', ')],
        ['label' => 'Jenis Kelamin', 'value' => $c['gender']],
    ], 160) !!}

    <div class="sec-title">C.&nbsp;&nbsp;DATA IBU KANDUNG</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $m['nik']],
        ['label' => 'Nama Lengkap', 'value' => $m['name']],
        ['label' => 'Alamat', 'value' => $m['address']],
        ['cont' => true, 'label' => '', 'value' => $m['rt_rw']],
    ], 160) !!}

    <div class="sec-title">D.&nbsp;&nbsp;DATA AYAH KANDUNG</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $f['nik']],
        ['label' => 'Nama Lengkap', 'value' => $f['name']],
        ['label' => 'Alamat', 'value' => $f['address']],
        ['cont' => true, 'label' => '', 'value' => $f['rt_rw']],
    ], 160) !!}

    <div class="sec-title">E.&nbsp;&nbsp;DATA STATUS PERKAWINAN</div>
    {!! $renderRows([
        ['label' => 'Nomor Kutipan Akta Perkawinan', 'value' => $mar['certificate_number']],
        ['label' => 'Tanggal Pernikahan', 'value' => $mar['date']],
    ], 160) !!}

    <div class="sec-title">F.&nbsp;&nbsp;DATA PELAPOR</div>
    {!! $renderRows([
        ['label' => 'NIK', 'value' => $r['nik']],
        ['label' => 'Nama Lengkap', 'value' => $r['name']],
        ['label' => 'Alamat', 'value' => $r['address']],
        ['label' => 'NoHP/Telepon/Email Aktif', 'value' => $r['phone']],
        ['label' => 'Tanggal Permohonan', 'value' => $r['application_date']],
        ['label' => 'Tanda Tangan Pemohon', 'value' => ''],
    ], 160) !!}

    <div class="sec-title">G.&nbsp;&nbsp;DOKUMEN PERSYARATAN</div>
    <table class="doc-list">
        @foreach ($documents as $key => $label)
            <tr>
                <td class="doc-box">{{ in_array($key, $birth['documents'], true) ? 'X' : '' }}</td>
                <td class="doc-label">{{ $label }}</td>
            </tr>
        @endforeach
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 10pt; page-break-inside: avoid;">
        <tr>
            <td style="width: 58%;"></td>
            <td style="vertical-align: top;">
                Sleman, <span class="dash-line" style="width: 110pt;">&nbsp;</span><br>
                Petugas Penerima Berkas,
                <div style="height: 40pt;"></div>
                <span class="dash-line" style="width: 150pt;">&nbsp;</span>
            </td>
        </tr>
    </table>

</div>
@endsection