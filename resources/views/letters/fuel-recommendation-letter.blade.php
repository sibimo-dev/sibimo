@extends('letters.layouts.base')

@section('title', 'Surat Rekomendasi Pembelian Jenis BBM Tertentu')

@section('extra_css')
    /* Kompensasi surat ini agak panjang — dirapatkan supaya tetap 1 halaman */
    @page { margin: 1.6cm 2cm 1.4cm 2cm; }

    body { font-size: 10.6pt; line-height: 1.18; }

    p.gap-top { margin-top: 5pt; }
    p.gap-top-lg { margin-top: 10pt; }

    table.data td { padding: 1.5pt 0; }
    td.val { height: 12pt; }

    table.ttd td.ttd-content { padding-top: 14pt; }
    .ttd-space { height: 36pt; }
@endsection

@section('content')
    <div class="judul">Surat Rekomendasi Pembelian Jenis BBM Tertentu</div>
    <div class="nomor">Nomor : {{ $number }}</div>

    <p class="gap-top-lg">Dasar Hukum:</p>
    <p class="gap-top">1. Undang-Undang Nomor 22 Tahun 2001 tentang Minyak dan Gas Bumi;</p>
    <p>2. Undang-Undang Nomor 23 Tahun 2014 tentang Pemerintahan Daerah;</p>
    <p>3. Peraturan Presiden Nomor 191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga
        Jual Eceran Bahan Bakar Minyak sebagaimana telah diubah dengan Peraturan Presiden Nomor
        191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga Jual Eceran Bahan Bakar
        Minyak;</p>

    <p class="gap-top-lg">Dengan ini memberikan rekomendasi kepada:</p>

    <table class="data" style="margin-top:6pt;">
        <tr><td class="lbl">Nama</td><td class="sep">:</td><td class="val">{{ $applicant['name'] ?? '' }}</td></tr>
        <tr><td class="lbl">Alamat Usaha</td><td class="sep">:</td><td class="val">{{ $business['address'] ?? '' }}</td></tr>
        <tr><td class="lbl">Konsumen</td><td class="sep">:</td><td class="val">{{ $form['consumer'] ?? '' }}</td></tr>
        <tr><td class="lbl">Jenis Usaha Kegiatan</td><td class="sep">:</td><td class="val">{{ $business['type'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top-lg">1. Berdasarkan hasil verifikasi, kebutuhan BBM digunakan untuk sarana sebagai berikut:</p>

    <table style="width:100%; border-collapse:collapse; margin-top:4pt; font-size:9pt;">
        <tr>
            <th style="border:1px solid #000; width:22pt; padding:2pt;">NO</th>
            <th style="border:1px solid #000; padding:2pt;">Pengecer Solar</th>
            <th style="border:1px solid #000; width:95pt; padding:2pt;">BBM Jenis Tertentu</th>
            <th style="border:1px solid #000; width:130pt; padding:2pt;">Konsumen BBM Jenis Tertentu<br>(Liter/Jam/Hari/Minggu/Bulanan)</th>
        </tr>
        @forelse (($form['fuel_items'] ?? []) as $i => $row)
            <tr>
                <td style="border:1px solid #000; text-align:center; padding:2pt;">{{ $i + 1 }}</td>
                <td style="border:1px solid #000; padding:2pt;">{{ $row['retailer'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:2pt;">{{ $row['type'] ?? '' }}</td>
                <td style="border:1px solid #000; padding:2pt;">{{ $row['description'] ?? '' }}</td>
            </tr>
        @empty
            @for ($i = 0; $i < 2; $i++)
                <tr>
                    <td style="border:1px solid #000; height:14pt; text-align:center; padding:2pt;">{{ $i + 1 }}</td>
                    <td style="border:1px solid #000; padding:2pt;"></td>
                    <td style="border:1px solid #000; padding:2pt;"></td>
                    <td style="border:1px solid #000; padding:2pt;"></td>
                </tr>
            @endfor
        @endforelse
        <tr>
            <td colspan="3" style="border:1px solid #000; text-align:center; padding:2pt;">Jumlah</td>
            <td style="border:1px solid #000; padding:2pt;">{{ $form['total'] ?? '' }}</td>
        </tr>
    </table>

    <p class="gap-top-lg">2. Diberikan Jenis BBM Tertentu Jenis Minyak Solar (Gas Oil)</p>
    <table class="data" style="margin-top:3pt;">
        <tr><td class="lbl">Alokasi Volume</td><td class="sep">:</td><td class="val">{{ $form['volume_allocation'] ?? '' }}</td></tr>
        <tr><td class="lbl">Tempat Pengambilan</td><td class="sep">:</td><td class="val">{{ $form['pickup_location'] ?? '' }}</td></tr>
        <tr><td class="lbl">Nomor Lembaga Penyalur</td><td class="sep">:</td><td class="val">{{ $form['distributor_number'] ?? '' }}</td></tr>
        <tr><td class="lbl">Lokasi</td><td class="sep">:</td><td class="val">{{ $form['distributor_location'] ?? '' }}</td></tr>
    </table>

    <p class="gap-top-lg">3. Apabila penggunaan Surat Rekomendasi ini tidak sebagaimana mestinya, maka akan
        dicabut dan ditindaklanjuti dengan proses hukum sesuai dengan ketentuan dan peraturan
        perundang-undangan.</p>
@endsection