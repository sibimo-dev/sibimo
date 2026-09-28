@extends('letters.layouts.form')

@section('title', 'Surat Pernyataan Permohonan Data Letter C')

@section('extra_css')
        /* Surat tanpa kop: pakai layout form, tapi tampilan disesuaikan seperti surat biasa */
        @page { margin: 1.5cm 2cm 1.5cm 2cm; }
        body { font-size: 11pt; line-height: 1.3; }
        .kode-form { display: none; }
        .judul-form { font-size: 13.3pt; text-decoration: underline; margin-top: 0; }

        p { margin: 0; }
        p.gap-top { margin-top: 9pt; }
        p.gap-top-lg { margin-top: 18pt; }
        p.justify { text-align: justify; }

        /* Tabel data "Label : Nilai" dengan garis putus-putus di bawah nilai */
        table.data { width: 400pt; margin-left: 40pt; border-collapse: collapse; margin-top: 6pt; }
        table.data td { padding: 2.5pt 0; vertical-align: top; }
        table.data td.lbl { width: 105pt; }
        table.data td.sep { width: 11pt; }
        table.data td.val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 14pt; }

        /* Blok tanda tangan 2 kolom */
        table.sign { width: 100%; border-collapse: collapse; page-break-inside: avoid; margin-top: 24pt; }
        table.sign td { padding: 0; vertical-align: top; font-size: 11pt; }
        table.sign td.col-left { width: 66%; }
        table.sign td.col-right { width: 34%; }
        table.sign td.nowrap { white-space: nowrap; }
        table.sign td.sign-name { padding-top: 34pt; }
        table.sign td.materai { padding-top: 4pt; padding-left: 20pt; font-size: 9pt; }
@endsection

@section('content')
    @php
        // Data kosong tampil sebagai titik-titik, supaya bisa ditulis tangan.
        $fill = fn ($value, $length = 25) => filled($value) ? $value : str_repeat('.', $length);

        $rows = [
            ['Nama', $applicant['name']],
            ['NIK', $applicant['nik']],
            ['Tempat/ Tgl. Lahir', $applicant['birth']],
            ['Jenis Kelamin', $applicant['gender']],
            ['Alamat', $applicant['address']],
            ['Pekerjaan', $applicant['occupation']],
        ];
    @endphp

    <div class="judul-form">Surat Pernyataan Permohonan Data Letter C</div>

    <p class="gap-top-lg">Yang bertanda tangan di bawah ini:</p>

    <table class="data">
        @foreach ($rows as [$label, $value])
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="sep">:</td>
                <td class="val">{{ $value }}&nbsp;</td>
            </tr>
        @endforeach
    </table>

    <p class="gap-top-lg justify">
        Dengan ini menyatakan bahwa saya mengajukan permohonan data Letter C yang berada di
        Padukuhan {{ $fill($letter_c['hamlet'], 15) }}, Kalurahan Bimomartani, Kapanewon Ngemplak,
        Kabupaten Sleman.
    </p>

    <p class="gap-top">Data Letter C tersebut akan dipergunakan untuk keperluan:</p>

    <p class="gap-top">
        Warisan/Konversi atas nama {{ $fill($letter_c['owner_name'], 15) }} (diisi nama pemilik C)
    </p>

    <p class="gap-top justify">
        Saya menyatakan bahwa seluruh keterangan yang saya berikan adalah benar. Apabila di kemudian
        hari terdapat ketidaksesuaian atau penyalahgunaan atas data yang diperoleh, maka sepenuhnya
        menjadi tanggung jawab saya dan tidak akan menuntut Pemerintah Kalurahan Bimomartani maupun
        pihak lain.
    </p>

    <p class="gap-top justify">
        Demikian Surat Pernyataan ini saya buat dengan sebenar-benarnya untuk dipergunakan
        sebagaimana mestinya.
    </p>

    <table class="sign">
        <tr>
            <td class="col-left"></td>
            <td class="col-right nowrap">{{ $signature['city'] }}, {{ $signature['date'] }}</td>
        </tr>
        <tr>
            <td class="col-left">Mengetahui</td>
            <td class="col-right">Pemohon,</td>
        </tr>
        <tr>
            <td class="col-left">Dukuh {{ $fill($letter_c['hamlet'], 18) }}</td>
            <td class="col-right"></td>
        </tr>
        <tr>
            <td class="col-left"></td>
            <td class="col-right materai">Materai 10.000</td>
        </tr>
        <tr>
            <td class="col-left sign-name">{{ $fill($hamlet_head_name, 28) }}</td>
            <td class="col-right sign-name">{{ $fill($applicant['name'], 28) }}</td>
        </tr>
    </table>
@endsection