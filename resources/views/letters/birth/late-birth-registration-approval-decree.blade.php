@extends('letters.layouts.base')

@php
    // Kop Dukcapil (varian 'dukcapil' di letters.layouts.base).
    $kop = [
        'type' => 'dukcapil',
        'line1' => 'PEMERINTAH KABUPATEN SLEMAN',
        'line2' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',
        'address' => 'Jalan KRT Pringgodiningrat No. 3 Beran, Tridadi, Sleman , DIY, 55511',
        'contact' => 'Telepon ( 0274 ) 868362, Faksimile ( 0274 ) 868945',
        'web_email' => 'website : www.capil.slemankab.go.id, E-mail : dukcapil@slemankab.go.id',
    ];

    $child = $birth['child'];
    $decree = $birth['decree'] ?? [];
    $requestDate = $birth['reporter']['application_date'] ?: '..........................';
    $dots = '..........................';
    $nb = "\u{00A0}";
@endphp

@section('title', 'Keputusan Persetujuan Pencatatan Kelahiran Terlambat')

@section('extra_css')
    /* Font Times New Roman untuk seluruh surat ini (menimpa font default layout,
       ditulis di selector body milik section extra_css sendiri supaya berlaku). */
    body { font-family: "Times New Roman", Times, serif; font-size: 10pt; line-height: 1.25; }
    .head-block { text-align: center; margin-top: 8pt; font-weight: bold; }
    table.dec { width: 100%; border-collapse: collapse; margin-top: 6pt; }
    table.dec td { vertical-align: top; padding: 1pt 0; text-align: justify; }
    table.dec td.k { width: 62pt; font-weight: bold; }
    table.dec td.c { width: 8pt; }
    table.dec td.n { width: 16pt; }
    table.dec td.n2 { width: 14pt; }
    table.data-dec { width: 100%; border-collapse: collapse; }
    table.data-dec td { padding: 1pt 0; vertical-align: top; }
    table.data-dec td.l { width: 140pt; }
    table.data-dec td.s { width: 10pt; }
    table.data-dec td.v { border-bottom: 1px dashed #000; font-weight: bold; }
    table.sign-dec { width: 100%; border-collapse: collapse; margin-top: 10pt; page-break-inside: avoid; }
    table.sign-dec td.sp { width: 60%; }
    .sign-dec-space { height: 44pt; }
@endsection

@section('content')
    <div style="margin-top: 10pt; font-weight: bold;">
        KEPUTUSAN KEPALA DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL KABUPATEN SLEMAN<br>
        Nomor:{{ $nb }}{{ $decree['number'] ?? '' }}{{ $nb }}/Ken.Ka.Disdukcapil/AL -TLB/
    </div>

    <div class="head-block">
        tentang<br>
        PERSETUJUAN PENCATATAN KELAHIRAN TERLAMBAT<br>
        KEPALA DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL KABUPATEN SLEMAN
    </div>

    <table class="dec" style="margin-top: 12pt;">
        <tr>
            <td class="k">Membaca</td><td class="c">:</td>
            <td colspan="2">Surat Pelaporan Saudara <u>{{ $birth['reporter']['name'] ?: $dots }}</u>
                Tanggal {{ $requestDate }}</td>
        </tr>
        <tr>
            <td class="k">Menimbang</td><td class="c">:</td>
            <td class="n">a.</td>
            <td>bahwa dokumen permohonan persetujuan pencatatan kelahiran terlambat atas nama
                <b><u>{{ $child['name'] ?: $dots }}</u></b> telah lengkap;</td>
        </tr>
        <tr><td></td><td></td><td class="n">b.</td>
            <td>bahwa berdasarkan ketentuan angka 2 surat edaran Menteri Dalam Negeri Nomor 472.11/2304/SJ
                tanggal 6 Mei 2013 perihal Tindak Lanjut Pelaksanaan Keputusan Mahkamah Konstitusi Nomor
                18/PUU-XI/2013, Pelaporan Kelahiran yang melampaui batas waktu 60 ( enam puluh ) hari sejak
                tanggal kelahiran, pencatatan kelahirannya dilaksanakan setelah mendapatkan keputusan Kepala
                Dinas Kependudukan Dan Pencatatan Sipil ;</td></tr>
        <tr><td></td><td></td><td class="n">c.</td>
            <td>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan huruf b perlu menetapkan
                Keputusan Kepala Dinas Kependudukan dan Pencatatan Sipil tentang persetujuan Pencatatan
                Terlambat.</td></tr>
        <tr>
            <td class="k">Mengingat</td><td class="c">:</td>
            <td class="n">1.</td>
            <td>Undang – Undang Nomor 15 Tahun 1950 tentang Pembentukan Daerah Kabupaten dalam Lingkungan
                Daerah Istimewa Yogyakarta jo. Peraturan Pemerintah Nomor 32 Tahun 1950;</td>
        </tr>
        <tr><td></td><td></td><td class="n">2.</td>
            <td>Undang-Undang Nomor 23 Tahun 2006 tentang Administrasi Kependudukan sebagaimana telah diubah
                dengan Undang-Undang Nomor 24 Tahun 2013 tentang Perubahan Atas Undang-Undang Nomor 23 Tahun
                2006 tentang Administrasi Kependudukan ;</td></tr>
        <tr><td></td><td></td><td class="n">3.</td>
            <td>Undang- Undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah sebagaimana telah diubah terakhir
                dengan Undang- Undang Nomor 9 Tahun 2015 tentang Perubahan Kedua atas Undang-Undang Nomor 23
                Tahun 2014 tentang Pemerintahan Daerah;</td></tr>
        <tr><td></td><td></td><td class="n">4.</td>
            <td>Peraturan Daerah Kabupaten Sleman Nomor 20 Tahun 2019 tentang Penyelenggaraan Administrasi
                Kependudukan.</td></tr>
    </table>

    <table class="dec" style="margin-top: 10pt;">
        <tr>
            <td class="k">Memperhatikan</td><td class="c">:</td>
            <td>Surat Edaran Menteri Dalam Negeri Nomor 472.11/2304/SJ tanggal 06 Mei 2013 perihal Tindaklanjut
                Pelaksanaan Putusan Mahkamah Konstitusi Nomor 18/PUU-XI/2013;</td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; margin: 8pt 0 4pt;">MEMUTUSKAN</div>

    <table class="dec">
        <tr>
            <td class="k">Menetapkan</td><td class="c">:</td><td></td>
        </tr>
        <tr>
            <td class="k">KESATU</td><td class="c">:</td>
            <td>Persetujuan pencatatan kelahiran terlambat dalam Akta Kelahiran dengan data sebagai berikut
                <table class="data-dec" style="margin-top: 4pt;">
                    <tr><td class="l">Nama</td><td class="s">:</td><td class="v">{{ $child['name'] }}</td></tr>
                    <tr><td class="l">NIK</td><td class="s">:</td><td class="v">{{ $child['nik'] }}</td></tr>
                    <tr><td class="l">Tempat Lahir dan Tanggal Lahir</td><td class="s">:</td>
                        <td class="v">{{ $child['birth_place'] }}{{ str_repeat($nb, 6) }}{{ $child['birth_date'] }}</td></tr>
                    <tr><td class="l">Anak Ke/ Jenis Kelamin</td><td class="s">:</td>
                        <td class="v">Anak Ke : {{ $child['birth_order'] }}{{ str_repeat($nb, 8) }}Jenis Kelamin : {{ $child['gender'] }}</td></tr>
                    <tr><td class="l">Nama Ibu</td><td class="s">:</td><td class="v">{{ $birth['mother']['name'] }}</td></tr>
                    <tr><td class="l">Nama Ayah</td><td class="s">:</td><td class="v">{{ $birth['father']['name'] }}</td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="k" style="padding-top: 6pt;">KEDUA</td><td class="c" style="padding-top: 6pt;">:</td>
            <td style="padding-top: 6pt;">Keputusan ini mulai berlaku pada tanggal ditetapkan</td>
        </tr>
    </table>
@endsection

{{-- Blok penandatangan: Kepala Dinas (bukan pejabat kalurahan), jadi menimpa TTD default. --}}
@section('custom_signature')
    <table class="sign-dec">
        <tr>
            <td class="sp"></td>
            <td>
                Ditetapkan di {{ $decree['place'] ?? 'Sleman' }}<br>
                Pada tanggal ...........................<br>
                Kepala Dinas
                <div class="sign-dec-space"></div>
                {{ $decree['agency_head_name'] ?? '' }}<br>
                NIP. {{ $decree['agency_head_nip'] ?? '' }}
            </td>
        </tr>
    </table>
@endsection