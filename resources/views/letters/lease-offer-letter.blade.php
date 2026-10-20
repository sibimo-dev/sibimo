{{--
    Surat Penawaran Sewa Kontrak Gedung
    Slug disarankan: 'lease-offer-letter' (alias lama: 'surat-penawaran-sewa')

    Field yang dipakai (disediakan LetterPdfService::viewData()/sampleViewData()):
      $number                     nomor surat
      $recipient                  nama/pihak yang dituju (form: recipient_name)
      $recipient_address          baris "Di :" (form: recipient_address)
      $lease['building_name']     nama gedung yang ditawarkan (form: lease_building_name)
      $lease['building_address']  alamat gedung (form: lease_building_address)
      $lease['duration_years']    lama sewa dalam tahun (form: lease_duration_years)

    Data penyewa, objek, dan nilai penawaran berasal dari normalisasi SPSKG di
    LetterPdfService. Titik hanya menjadi fallback bila pengajuan lama belum
    memiliki nilai pada field tersebut.
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Penawaran Sewa Kontrak Gedung')

@section('extra_css')
    /* baris atas: Nomor di kiri, kota+tanggal di kanan, satu baris */
    table.letter-top { width: 100%; border-collapse: collapse; margin-top: 4pt; }
    table.letter-top td { padding: 0; vertical-align: top; }
    table.letter-top td.date { text-align: right; }

    /* "Hal" pakai label+value dalam tabel supaya baris kedua (kalau perihalnya
       panjang dan wrap ke bawah) ikut menjorok sejajar dengan baris pertama,
       bukan mentok ke margin kiri halaman */
    table.hal { width: 100%; border-collapse: collapse; margin-top: 2pt; }
    table.hal td { padding: 0; vertical-align: top; }
    table.hal td.lbl { width: 32pt; }
    table.hal td.sep { width: 9pt; }

    /* Label "Nama"/"Jabatan"/"Alamat" jauh lebih pendek dari label default
       table.data (yang dirancang untuk label panjang seperti "Marital
       Status"), jadi lebar kolom labelnya dipersempit di sini. Garis
       kosongnya sendiri tetap pakai garis putus-putus bawaan class "data"
       (td.val) yang otomatis memanjang ke seluruh lebar kolom — sama seperti
       di surat-surat lain. */
    table.signer-data td.lbl { width: 55pt; }
@endsection

@section('content')

    <table class="letter-top">
        <tr>
            <td>Nomor : {{ $number }}</td>
            <td class="date">{{ $signature['city'] }}, {{ $signature['date_long'] }}</td>
        </tr>
    </table>
    <table class="hal">
        <tr>
            <td class="lbl">Hal</td>
            <td class="sep">:</td>
            <td><i>Penawaran Sewa Kontrak Gedung {{ $lease['building_name'] }}</i></td>
        </tr>
    </table>

    <p class="gap-top-lg">Kepada</p>
    <p>Yth. {{ $recipient }}</p>
    @if (filled($lease['tenant_name'] ?? null))
        <p>{{ $lease['tenant_name'] }}</p>
    @endif
    <p>Di {{ $recipient_address }}</p>

    <p class="gap-top-lg">Dengan hormat,</p>
    <p class="gap-top" style="text-align: justify">
        Pihak penyewa dalam surat ini diwakili oleh:
    </p>

    <table class="data signer-data" style="margin-top: 9pt">
        <tr>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $lease['tenant_representative'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jabatan</td>
            <td class="sep">:</td>
            <td class="val">{{ $lease['tenant_position'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $lease['tenant_address'] ?? '' }}</td>
        </tr>
    </table>

    <p class="gap-top-lg" style="text-align: justify">
        Sesuai dengan surat perjanjian sewa gedung, bahwa berakhirnya masa sewa
        {{ $lease['building_name'] }} yang beralamat di {{ $lease['building_address'] }}
        adalah tanggal {{ $lease['previous_end_date'] ?: '.........................' }}, dengan ini kami tawarkan sewa
        bangunan per tahun {{ $lease['price_per_year'] ?: 'Rp .........................' }}
        dengan lama sewa {{ $lease['duration_years'] ?: '.........................' }} tahun.
    </p>

    <p class="gap-top">
        Demikian permohonan ini kami haturkan, atas terkabulnya diucapkan terima kasih.
    </p>

@endsection
