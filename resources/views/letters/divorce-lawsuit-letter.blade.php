{{--
    Divorce Lawsuit Letter (Surat Gugat Cerai), same kop style as the other
    letters (with Javanese aksara — this letter does NOT hide it).
    Set letter_types.blade_view to: letters.divorce-lawsuit-letter

    This template shares LetterPdfService's common data contract, same as
    every other letter ($nomor, $applicant, $signer, $signature, $kop, $logo,
    $form). It additionally needs three extra top-level keys that only this
    letter type uses — see LetterPdfService::viewData()/sampleOverrides() for
    how they're built:

        $applicant['name', 'birth', 'religion', 'occupation', 'address',
                    'gender']            already provided generically — the
                                          person filing (menghadapkan diri)

        $spouse['name', 'birth', 'occupation', 'address',
                 'marriage_cert_number']  the spouse being sued (Suami/Istri)

        $reasons                         array of strings, numbered reason list;
                                          renders as 3 blank numbered lines
                                          (same dashed style as the data tables)
                                          when empty/not provided

        $witnesses                       array of exactly 2 items, each:
            ['name', 'birth', 'religion', 'occupation', 'address']

        $form['court_name'], $form['court_city']  optional, override the
            default addressee ("Ketua Pengadilan Agama Kab Sleman" / "Sleman")

    NOTE: the printed labels ("Dengan Hormat", "Nama", "Tempat tgl lhr", "Alasan
    Orang tersebut mengajukan Gugat Cerai/Rapak di karenakan", etc.) stay in
    Indonesian on purpose — this is the fixed wording of the official letter,
    not application code. Likewise $nomor/$applicant/$signer/$form/$kop keep
    the names already established by LetterPdfService's shared contract, so
    every existing letter keeps working — only the fields unique to this
    letter (spouse/reasons/witnesses, and their inner keys) are named fresh
    in English.

    CSS notes: kept deliberately simple/table-based (no flex, grid, or
    inline-block) since dompdf's CSS support is limited and those properties
    render inconsistently there. Nomor/Hal/Kepada Yth are stacked plain
    left-aligned lines (no two-column table). Each data table is wrapped in
    a .keep-together div with page-break-inside: avoid so a witness/applicant/
    spouse block never gets torn across a page break; overall spacing is also
    tightened a bit so the whole letter fits on one page.
--}}
@extends('letters.layouts.base')

@section('title', 'Surat Gugat Cerai')

@section('extra_css')
    /* Surat ini lebih padat isinya (2 tabel + 2 saksi) dibanding surat lain,
       jadi margin atas/bawah halaman dikecilkan KHUSUS untuk letter ini
       (tidak menyentuh base.blade.php, jadi surat lain tidak berubah).
       Kiri/kanan tetap 2cm supaya lebar teks konsisten dengan surat lain. */
    @page { margin: 1.2cm 2cm 1.2cm 2cm; }

    /* Konten surat sedikit lebih rapat dari surat lain supaya cukup 1 halaman */
    .letter-body { font-size: 10.2pt; line-height: 1.16; }
    .letter-body p.gap-top { margin-top: 4pt; }
    .letter-body p.gap-top-lg { margin-top: 6pt; }

    /* Nomor / Hal / Kepada Yth — ditumpuk, rata kiri semua, di bawah kop */
    .heading-block { margin-top: 8pt; }
    .heading-block div { margin: 0; }
    .heading-block .heading-gap { margin-top: 4pt; }

    /* Numbered "No. | Label | : | Value" data table (dashed underline like table.data) */
    table.numbered { width: 388pt; margin-left: 20pt; margin-top: 2pt; border-collapse: collapse; page-break-inside: avoid; }
    table.numbered td { padding: 1pt 0; vertical-align: top; }
    table.numbered td.num { width: 16pt; }
    table.numbered td.lbl { width: 95pt; }
    table.numbered td.sep { width: 11pt; }
    table.numbered td.val { border-bottom: 1px dashed #000; padding-left: 2pt; height: 11pt; }


    /* Bungkus tiap blok (paragraf + tabel) supaya tidak terpotong halaman */
    .keep-together { page-break-inside: avoid; }
@endsection

@section('content')
    <div class="letter-body">
    <div class="heading-block">
        <div>Nomor : {{ $nomor }}</div>
        <div>Hal &nbsp;&nbsp;&nbsp;: Gugat Cerai</div>
        <div class="heading-gap">Kepada Yth,</div>
        <div>{{ $form['court_name'] ?? 'Ketua Pengadilan Agama Kab Sleman' }}</div>
        <div>Di {{ $form['court_city'] ?? 'Sleman' }}.</div>
    </div>

    <p class="gap-top-lg">Dengan Hormat,</p>

    <p class="gap-top">Dengan ini menghadapkan seorang {{ $applicant['gender'] ?? 'Laki-Laki / Perempuan' }} :</p>
    <div class="keep-together">
    <table class="numbered">
        <tr>
            <td class="num">1.</td>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="lbl">Tempat tgl lhr</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['birth'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="lbl">Agama</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['religion'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['occupation'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $applicant['address'] ?? '' }}</td>
        </tr>
    </table>
    </div>

    <p class="gap-top">Orang tersebut menghadap ke Pengadilan Agama Kabupaten Sleman perlu mengajukan Cerai/Rapak kepada Suaminya/Istrinya :</p>
    <div class="keep-together">
    <table class="numbered">
        <tr>
            <td class="num">1.</td>
            <td class="lbl">Nama</td>
            <td class="sep">:</td>
            <td class="val">{{ $spouse['name'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">2.</td>
            <td class="lbl">Tempat tgl lhr</td>
            <td class="sep">:</td>
            <td class="val">{{ $spouse['birth'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">3.</td>
            <td class="lbl">Pekerjaan</td>
            <td class="sep">:</td>
            <td class="val">{{ $spouse['occupation'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">4.</td>
            <td class="lbl">Alamat</td>
            <td class="sep">:</td>
            <td class="val">{{ $spouse['address'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="num">5.</td>
            <td class="lbl">Surat Nikah</td>
            <td class="sep">:</td>
            <td class="val">{{ $spouse['marriage_cert_number'] ?? '' }}</td>
        </tr>
    </table>
    </div>

    <p class="gap-top">Alasan Orang tersebut mengajukan Gugat Cerai/Rapak di karenakan :</p>
    <div class="keep-together">
    <table class="numbered">
        @foreach ((($reasons ?? []) ?: [null, null, null]) as $reason)
            <tr>
                <td class="num">{{ $loop->iteration }}.</td>
                <td class="val" colspan="3">{{ $reason }}</td>
            </tr>
        @endforeach
    </table>
    </div>

    <p class="gap-top">Dalam Gugatan ini orang tersebut mengajukan dua orang saksi :</p>

    @foreach (($witnesses ?? [null, null]) as $index => $witness)
        <div class="keep-together" @if ($index === 1) style="margin-top: 5pt;" @endif>
        <table class="numbered">
            <tr>
                <td class="num">1.</td>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['name'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="num">2.</td>
                <td class="lbl">Tempat tgl lhr</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['birth'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="num">3.</td>
                <td class="lbl">Agama</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['religion'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="num">4.</td>
                <td class="lbl">Pekerjaan</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['occupation'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="num">5.</td>
                <td class="lbl">Alamat</td>
                <td class="sep">:</td>
                <td class="val">{{ $witness['address'] ?? '' }}</td>
            </tr>
        </table>
        </div>
    @endforeach

    <p class="gap-top">Semoga menjadikan maklum dan kepada Pengadilan Agama yang bersangkutan kami menyerahkan persoalan tersebut.</p>
    </div>
@endsection