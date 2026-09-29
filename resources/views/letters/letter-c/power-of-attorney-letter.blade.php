@extends('letters.layouts.form')

@section('title', 'Surat Kuasa')

@section('extra_css')
        /* Surat tanpa kop: pakai layout form, tapi tampilan disesuaikan seperti surat biasa.
           Surat ini panjang, font dikecilkan supaya muat 1 halaman folio. */
        @page { margin: 1.5cm 2cm 1.5cm 2cm; }
        body { font-size: 10.5pt; line-height: 1.25; }
        .kode-form { display: none; }
        .judul-form { font-size: 13.3pt; text-decoration: underline; margin-top: 0; }

        p { margin: 0; }
        p.gap-top { margin-top: 9pt; }
        p.justify { text-align: justify; }

        /* Tabel data "Label : Nilai" dengan garis putus-putus di bawah nilai */
        table.data { width: 100%; border-collapse: collapse; margin-top: 6pt; }
        table.data td { padding: 2.5pt 0; vertical-align: top; }
        table.data td.lbl { width: 90pt; }
        table.data td.sep { width: 11pt; }
        table.data td.val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 14pt; }

        table.numbered { width: 480pt; margin-left: 18pt; border-collapse: collapse; margin-top: 4pt; }
        table.numbered td { padding: 1.5pt 0; vertical-align: top; text-align: justify; }
        table.numbered td.num { width: 22pt; text-align: left; }

        /* Blok tanda tangan 2 kolom */
        table.sign { width: 100%; border-collapse: collapse; page-break-inside: avoid; }
        table.sign td { padding: 0; vertical-align: top; font-size: 10.5pt; }
        table.sign td.col-left { width: 66%; }
        table.sign td.col-right { width: 34%; }
        table.sign td.nowrap { white-space: nowrap; }
        table.sign td.sign-name { padding-top: 22pt; }

        table.materai-box { border-collapse: collapse; margin-top: 4pt; }
        table.materai-box td {
            border: 1px solid #000; padding: 3pt 8pt;
            font-size: 8pt; font-weight: bold; text-align: center; line-height: 1.2;
        }
@endsection

@section('content')
    @php
        // Data kosong tampil sebagai titik-titik, supaya bisa ditulis tangan.
        $fill = fn ($value, $length = 25) => filled($value) ? $value : str_repeat('.', $length);

        $grantorRows = [
            ['Nama', $applicant['name']],
            ['Tmpt/tgl. Lahir', $applicant['birth']],
            ['Jenis Kelamin', $applicant['gender']],
            ['NIK', $applicant['nik']],
            ['Alamat', $applicant['address']],
        ];

        $attorneyRows = [
            ['Nama', $attorney['name']],
            ['Tmpt/tgl. Lahir', $attorney['birth']],
            ['Jenis Kelamin', $attorney['gender']],
            ['NIK', $attorney['nik']],
            ['Alamat', $attorney['address']],
        ];

        $tasks = [
            'Mewakili <strong>PIHAK PERTAMA</strong> untuk hadir dalam sidang waris di Kalurahan Bimomartani, Ngemplak, Sleman.',
            'Mewakili <strong>PIHAK PERTAMA</strong> untuk menandatangani buku sidang waris di Kalurahan Bimomartani, Ngemplak, Sleman.',
            'Mewakili <strong>PIHAK PERTAMA</strong> Mengurus dokumen pertanahan/berkas turun waris di Kalurahan Bimomartani, Ngemplak, Sleman.',
        ];
    @endphp

    <div class="judul-form">Surat Kuasa</div>

    <p class="gap-top">Saya yang bertanda tangan dibawah ini:</p>

    <table class="data">
        @foreach ($grantorRows as [$label, $value])
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="sep">:</td>
                <td class="val">{{ $value }}&nbsp;</td>
            </tr>
        @endforeach
    </table>

    <p class="gap-top justify">
        Dalam hal ini bertindak sebagai dan atas nama ahli waris dari Almarhum/Almarhumah
        {{ $fill($deceased['name'], 25) }} yang telah meninggal dunia di
        {{ $fill($deceased['death_place'], 25) }}, untuk berikutnya disebut <strong>PIHAK PERTAMA</strong>.
    </p>

    <p class="gap-top">Dengan ini memberikan kuasa kepada pihak KEDUA:</p>

    <table class="data">
        @foreach ($attorneyRows as [$label, $value])
            <tr>
                <td class="lbl">{{ $label }}</td>
                <td class="sep">:</td>
                <td class="val">{{ $value }}&nbsp;</td>
            </tr>
        @endforeach
    </table>

    {{-- Pemisah "---- KHUSUS ----" --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 8pt;">
        <tr>
            <td style="width: 44%; vertical-align: middle;">
                <div style="border-top: 1px dashed #000; height: 0; font-size: 0; line-height: 0;"></div>
            </td>
            <td style="width: 12%; text-align: center; font-weight: bold; vertical-align: middle;">KHUSUS</td>
            <td style="width: 44%; vertical-align: middle;">
                <div style="border-top: 1px dashed #000; height: 0; font-size: 0; line-height: 0;"></div>
            </td>
        </tr>
    </table>

    <p class="gap-top justify">
        Untuk dan atas nama <strong>PIHAK PERTAMA</strong> yang telah disebut diatas, maka
        <strong>PIHAK KEDUA</strong> dapat melakukan hal sebagai berikut:
    </p>

    <table class="numbered">
        @foreach ($tasks as $i => $task)
            <tr>
                <td class="num">{{ $i + 1 }})</td>
                <td>{!! $task !!}</td>
            </tr>
        @endforeach
    </table>

    <p class="gap-top justify">
        Demikian surat kuasa ini dibuat dengan sebenar-benarnya dan bisa digunakan sebagaimana
        mestinya.
    </p>

    <table class="sign" style="margin-top: 14pt;">
        <tr>
            <td class="col-left"></td>
            <td class="col-right nowrap">{{ $signature['city'] }}, {{ $signature['date_long'] }}</td>
        </tr>
        <tr>
            <td class="col-left">Penerima Kuasa</td>
            <td class="col-right">Pemberi Kuasa</td>
        </tr>
        <tr>
            <td class="col-left"></td>
            <td class="col-right">
                <table class="materai-box"><tr><td>Materai Rp.<br>10.000</td></tr></table>
            </td>
        </tr>
        <tr>
            <td class="col-left sign-name">({{ $fill($attorney['name'], 25) }})</td>
            <td class="col-right sign-name">({{ $fill($applicant['name'], 25) }})</td>
        </tr>
    </table>

    <table class="sign" style="margin-top: 16pt;">
        <tr>
            <td style="text-align: center;">
                Mengetahui<br>
                Kelurahan/Notaris {{ $fill($endorser['office'], 15) }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding-top: 34pt;">({{ $fill($endorser['name'], 25) }})</td>
        </tr>
    </table>
@endsection