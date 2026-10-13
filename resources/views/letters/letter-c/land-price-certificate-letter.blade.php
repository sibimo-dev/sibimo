@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Harga Tanah')

@section('extra_css')
        p.justify { text-align: justify; }

        /* Penandatangan (Nama / Jabatan) */
        table.signer { width: 100%; border-collapse: collapse; margin-top: 6pt; margin-left: 12pt; }
        table.signer td { padding: 3pt 0; vertical-align: top; }
        table.signer td.s-label { width: 60pt; }
        table.signer td.s-sep { width: 12pt; }

        /* Keterangan tanah */
        table.land { width: 100%; border-collapse: collapse; margin-top: 6pt; margin-left: 12pt; }
        table.land td { padding: 3pt 0; vertical-align: top; }
        table.land td.c1 { width: 170pt; }
        table.land td.c2 { width: 110pt; }
        table.land td.c-sep { width: 12pt; }
@endsection

@section('content')
    @php
        // Data kosong tampil sebagai titik-titik, supaya bisa ditulis tangan.
        $fill = fn ($value, $length = 25) => filled($value) ? $value : str_repeat('.', $length);
    @endphp

    <div class="judul">SURAT KETERANGAN HARGA TANAH</div>
    <div class="nomor">Nomor: {{ $number }}</div>

    <p class="gap-top-lg">Yang bertanda tangan dibawah ini:</p>

    <table class="signer">
        <tr>
            <td class="s-label">Nama</td>
            <td class="s-sep">:</td>
            <td>{{ $signature['name'] }}&nbsp;</td>
        </tr>
        <tr>
            <td class="s-label">Jabatan</td>
            <td class="s-sep">:</td>
            {{-- Jabatan sengaja dikosongkan (diisi tulis tangan / nanti dari SIBIMO). --}}
            <td>&nbsp;</td>
        </tr>
    </table>

    <p class="gap-top justify">
        Dengan ini menerangkan bahwa harga jual tanah atas dasar transaksi umum untuk tanah dengan
        keterangan sbb:
    </p>

    <table class="land">
        <tr>
            <td class="c1">No. Sertifikat/NIB</td>
            <td class="c-sep">:</td>
            <td colspan="3">{{ $fill($land['certificate_number'], 30) }}</td>
        </tr>
        <tr>
            <td class="c1">Luas</td>
            <td class="c-sep">:</td>
            <td colspan="3">{{ $fill($land['area'], 10) }} m²</td>
        </tr>
        <tr>
            <td class="c1">Nama Pemilik</td>
            <td class="c-sep">:</td>
            <td colspan="3">{{ $fill($land['owner_name'], 30) }}</td>
        </tr>
        <tr>
            <td class="c1">Letak Tanah</td>
            <td class="c-sep">:</td>
            <td class="c2">a. Padukuhan</td>
            <td class="c-sep">:</td>
            <td>{{ $fill($land['hamlet'], 25) }}</td>
        </tr>
        <tr>
            <td class="c1"></td>
            <td class="c-sep"></td>
            <td class="c2">b. Kalurahan/Desa</td>
            <td class="c-sep">:</td>
            <td>{{ $fill($land['village'], 25) }}</td>
        </tr>
        <tr>
            <td class="c1"></td>
            <td class="c-sep"></td>
            <td class="c2">c. Kapanewon</td>
            <td class="c-sep">:</td>
            <td>{{ $fill($land['district'], 25) }}</td>
        </tr>
        <tr>
            <td class="c1"></td>
            <td class="c-sep"></td>
            <td class="c2">d. Kota/Kabupaten</td>
            <td class="c-sep">:</td>
            <td>{{ $fill($land['regency'], 25) }}</td>
        </tr>
        <tr>
            <td class="c1">Harga Tanah Pasaran Berkisar</td>
            <td class="c-sep">:</td>
            <td colspan="3">
                {{ $land['price_min'] ?: 'Rp. ' . str_repeat('.', 15) }}
                s/d
                {{ $land['price_max'] ?: 'Rp. ' . str_repeat('.', 15) }}
            </td>
        </tr>
    </table>

    <p class="gap-top-lg justify">
        Demikian Surat Keterangan ini dibuat dengan sebenarnya, agar dapat dipergunakan dengan
        sebagaimana mestinya.
    </p>
@endsection