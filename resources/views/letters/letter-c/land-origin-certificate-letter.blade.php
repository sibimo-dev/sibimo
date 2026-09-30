@extends('letters.layouts.base')

@section('title', 'Surat Keterangan Asal Tanah')

@section('extra_css')
        p.justify { text-align: justify; }
        p.spaced { line-height: 1.7; }

        /* Penandatangan (Nama / Jabatan) */
        table.signer { width: 100%; border-collapse: collapse; margin-top: 6pt; margin-left: 20pt; }
        table.signer td { padding: 4pt 0; vertical-align: top; }
        table.signer td.s-label { width: 60pt; }
        table.signer td.s-sep { width: 12pt; }
@endsection

@section('content')
    @php
        // Data kosong tampil sebagai titik-titik, supaya bisa ditulis tangan.
        $fill = fn ($value, $length = 25) => filled($value) ? $value : str_repeat('.', $length);
    @endphp

    <div class="judul">SURAT KETERANGAN ASAL TANAH</div>
    <div class="nomor">Nomor: {{ $number }}</div>

    <p class="gap-top-lg">Yang bertanda tangan di bawah ini :</p>

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

    <p class="gap-top justify spaced">
        Dengan ini menerangkan bahwa Sertifikat Hak Milik Nomor: {{ $fill($land['certificate_number'], 20) }},
        tertulis atas nama {{ $fill($land['owner_name'], 25) }}, seluas {{ $fill($land['area'], 10) }} m²
        ({{ $fill($land['area_in_words'], 22) }}) surat ukur nomor
        {{ $fill($land['measurement_letter_number'], 12) }} tanggal
        {{ $fill($land['measurement_letter_date'], 18) }}, yang terletak di Dusun
        {{ $fill($land['hamlet'], 12) }} Kalurahan Bimomartani, Kecamatan Ngemplak, Kabupaten Sleman,
        Provinsi Daerah Istimewa Yogyakarta. Benar-benar tanah tersebut diperoleh dari harta bawaan
        atau harta warisan.
    </p>

    <p class="gap-top justify spaced">
        Demikian surat keterangan asal tanah ini dibuat dengan sebenarnya untuk dipergunakan
        sebagaimana mestinya.
    </p>
@endsection