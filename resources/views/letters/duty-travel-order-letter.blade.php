@extends('letters.layouts.base')

@section('title', 'Duty Travel Order Letter')

{{--
    Expected variables (map these in LetterPdfService, sampleOverrides, etc.):
    $kop, $logo   -> handled automatically by letters.layouts.base, no need to render here
    $travelOrder = [
        'issuingOfficial'      => official who authorizes the order,
        'employeeName'         => name of the employee ordered to travel,
        'employeeRank'         => rank/grade,
        'employeePosition'     => position,
        'travelLevel'          => travel class per regulation (default 'Biasa'),
        'purpose'              => purpose of the duty travel,
        'transport'            => mode of transport,
        'departurePlace'       => place of departure,
        'destinationPlace'     => destination place,
        'duration'             => trip duration,
        'departureDate'        => departure date,
        'returnDueDate'        => date the employee must be back,
        'companions'           => accompanying persons,
        'budgetAgency'         => agency charged for the budget,
        'budgetItem'           => budget line item,
        'notes'                => other remarks,
        'number'               => SPPD number (e.g. "0/BMM/0/2026"),
        'departureFrom'        => section I: departing from,
        'departureTo'          => section I: to,
        'departureOrderDate'   => section I: dated,
        'departureSigner'      => section I: signer name (top right),
        'arrivalAt'            => section II left: arrived at,
        'arrivalFrom'          => section II left: from,
        'arrivalDate'          => section II left: dated,
        'returnDepartureFrom'  => section II right: departing from,
        'returnDepartureTo'    => section II right: to,
        'returnDepartureDate'  => section II right: dated,
        'arrivalSigner'        => section II left signer name,
        'returnSigner'         => section II right signer name,
        'returnArrivalAt'      => section III: arrived back at,
        'returnArrivalDate'    => section III: dated,
    ]
--}}

@section('extra_css')
    .dto-title {
        text-align: center; font-weight: bold; text-decoration: underline;
        font-size: 11.5pt; margin-top: 8pt;
    }

    table.dto-grid { width: 100%; border-collapse: collapse; margin-top: 6pt; }
    table.dto-grid td {
        border: 1px solid #000; padding: 4pt 7pt; vertical-align: top; font-size: 9.3pt;
    }
    table.dto-grid td.no { width: 14pt; text-align: center; }
    table.dto-grid td.label { width: 190pt; }
    table.dto-grid td.val { width: auto; }
    .dto-sub { margin-left: 8pt; }

    /* Garis hitam pemisah HANYA muncul di antara dua seksi berurutan
       (I->II dan II->III), tidak di atas seksi I yang pertama. */
    .roman-block { width: 100%; margin-top: 10pt; padding-top: 0; }
    table.roman-block + table.roman-block {
        border-top: 1.3pt solid #000;
        padding-top: 6pt;
    }
    table.roman-tbl { width: 100%; border-collapse: collapse; }
    table.roman-tbl td { padding: 1pt 0; vertical-align: top; font-size: 9.3pt; }
    td.roman-no { width: 16pt; font-weight: bold; }
    td.rlabel { width: 105pt; }
    td.rsep { width: 8pt; }

    table.two-col { width: 100%; border-collapse: collapse; }
    table.two-col td { width: 50%; vertical-align: top; padding: 0 4pt 0 0; }
    table.two-col td.right-col { padding: 0 0 0 4pt; }

    /* Tabel isian per seksi: label dipersempit, nilai diberi sedikit
       padding-left saja supaya jarak setelah titik dua tidak terlalu jauh. */
    table.roman-sub { width: 100%; border-collapse: collapse; }
    table.roman-sub td { padding: 1pt 0; vertical-align: top; font-size: 9.3pt; }
    table.roman-sub td.rlabel { width: 80pt; }
    table.roman-sub td.rsep { width: 8pt; }
    table.roman-sub td.rval { padding-left: 2pt; }

    /* Baris nama penandatangan: PAKAI STRUKTUR YANG SAMA PERSIS dengan
       roman-sub (rlabel/rsep/rval) supaya nama otomatis jatuh tepat
       sejajar dengan posisi nilai field di atasnya (setelah titik dua),
       bukan diatur pakai angka padding manual yang gampang meleset. */
    table.signer-row { width: 100%; border-collapse: collapse; margin-top: 4pt; }
    table.signer-row td { padding: 1pt 0; vertical-align: top; font-size: 9.3pt; }
    table.signer-row td.rlabel { width: 80pt; }
    table.signer-row td.rsep { width: 8pt; }
    table.signer-row td.rval { padding-left: 2pt; font-weight: bold; }
    /* Jarak kosong SEBELUM nama (ruang untuk tanda tangan asli) diatur lewat
       padding-top pada kolom rval. Section II lebih tinggi -> jaraknya lebih
       besar dari Section I. Ubah angka ini untuk menambah/mengurangi jarak. */
    table.signer-row.gap-sm td.rval { padding-top: 50pt; }
    table.signer-row.gap-lg td.rval { padding-top: 50pt; }

    p.verif { margin-top: 4pt; font-size: 9.3pt; }

    /* Surat ini sudah punya blok tanda tangan sendiri di Seksi I & II,
       jadi blok tanda tangan umum dari layouts.base (di bagian paling
       bawah halaman/lembar terakhir) tidak diperlukan dan disembunyikan. */
    table.ttd { display: none !important; }
@endsection

@section('content')

    <div class="dto-title">SURAT PERINTAH PERJALANAN DINAS</div>

    {{-- ===== TABLE 1-9 ===== --}}
    <table class="dto-grid">
        <tr>
            <td class="no">1</td>
            <td class="label">Pejabat yang berwenang memberi perintah</td>
            <td class="val">{{ $travelOrder['issuingOfficial'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="no">2</td>
            <td class="label">
                Nama Pegawai yang diperintah<br>
                <span class="dto-sub">a. Pangkat/Golongan</span><br>
                <span class="dto-sub">b. Jabatan</span><br>
                <span class="dto-sub">c. Tingkat menurut peraturan perjalanan</span>
            </td>
            <td class="val">
                {{ $travelOrder['employeeName'] ?? '' }}<br>
                {{ $travelOrder['employeeRank'] ?? '' }}<br>
                {{ $travelOrder['employeePosition'] ?? '' }}<br>
                {{ $travelOrder['travelLevel'] ?? 'Biasa' }}
            </td>
        </tr>
        <tr>
            <td class="no">3</td>
            <td class="label">Maksud perjalanan dinas</td>
            <td class="val">{{ $travelOrder['purpose'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="no">4</td>
            <td class="label">Alat angkutan yang digunakan</td>
            <td class="val">{{ $travelOrder['transport'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="no">5</td>
            <td class="label">
                <span class="dto-sub">a. Tempat berangkat</span><br>
                <span class="dto-sub">b. Tempat tujuan</span>
            </td>
            <td class="val">
                {{ $travelOrder['departurePlace'] ?? '' }}<br>
                {{ $travelOrder['destinationPlace'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="no">6</td>
            <td class="label">
                <span class="dto-sub">a. Lama Perjalanan</span><br>
                <span class="dto-sub">b. Tanggal Berangkat</span><br>
                <span class="dto-sub">c. Tanggal harus kembali</span>
            </td>
            <td class="val">
                {{ $travelOrder['duration'] ?? '' }}<br>
                {{ $travelOrder['departureDate'] ?? '' }}<br>
                {{ $travelOrder['returnDueDate'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="no">7</td>
            <td class="label">Pengikut</td>
            <td class="val">{{ $travelOrder['companions'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="no">8</td>
            <td class="label">
                Pembebanan Anggaran<br>
                <span class="dto-sub">a. Instansi</span><br>
                <span class="dto-sub">b. Mata Anggaran</span>
            </td>
            <td class="val">
                <br>
                {{ $travelOrder['budgetAgency'] ?? '' }}<br>
                {{ $travelOrder['budgetItem'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td class="no">9</td>
            <td class="label">Keterangan lain-lain</td>
            <td class="val">{{ $travelOrder['notes'] ?? '' }}</td>
        </tr>
    </table>

    {{-- ===== SECTION I ===== --}}
    <table class="roman-tbl roman-block">
        <tr>
            <td class="roman-no">I</td>
            <td>
                <table class="roman-sub">
                    <tr><td class="rlabel">SPPD Nomor</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['number'] ?? '' }}</td></tr>
                    <tr><td class="rlabel">Berangkat Dari</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['departureFrom'] ?? '' }}</td></tr>
                    <tr><td class="rlabel">Ke</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['departureTo'] ?? '' }}</td></tr>
                    <tr><td class="rlabel">Pada Tanggal</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['departureOrderDate'] ?? '' }}</td></tr>
                </table>
                {{-- Nama penandatangan: pakai struktur rlabel/rsep/rval yang sama
                     dengan tabel field di atasnya, supaya otomatis sejajar tepat
                     setelah posisi titik dua (bukan diatur manual). --}}
                <table class="signer-row gap-sm">
                    <tr><td class="rlabel"></td><td class="rsep"></td><td class="rval">{{ $travelOrder['departureSigner'] ?? '' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== SECTION II ===== --}}
    <table class="roman-tbl roman-block">
        <tr>
            <td class="roman-no">II</td>
            <td>
                <table class="two-col">
                    <tr>
                        <td>
                            <table class="roman-sub">
                                <tr><td class="rlabel">Tiba Di</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['arrivalAt'] ?? '' }}</td></tr>
                                <tr><td class="rlabel">Dari</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['arrivalFrom'] ?? '' }}</td></tr>
                                <tr><td class="rlabel">Pada Tanggal</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['arrivalDate'] ?? '' }}</td></tr>
                            </table>
                        </td>
                        <td class="right-col">
                            <table class="roman-sub">
                                <tr><td class="rlabel">Berangkat dari</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['returnDepartureFrom'] ?? '' }}</td></tr>
                                <tr><td class="rlabel">Ke</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['returnDepartureTo'] ?? '' }}</td></tr>
                                <tr><td class="rlabel">Pada Tanggal</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['returnDepartureDate'] ?? '' }}</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table class="two-col">
                    <tr>
                        <td>
                            <table class="signer-row gap-lg">
                                <tr><td class="rlabel"></td><td class="rsep"></td><td class="rval">{{ $travelOrder['arrivalSigner'] ?? '' }}</td></tr>
                            </table>
                        </td>
                        <td class="right-col">
                            <table class="signer-row gap-lg">
                                <tr><td class="rlabel"></td><td class="rsep"></td><td class="rval">{{ $travelOrder['returnSigner'] ?? '' }}</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== SECTION III ===== --}}
    <table class="roman-tbl roman-block">
        <tr>
            <td class="roman-no">III</td>
            <td>
                <table class="roman-sub">
                    <tr><td class="rlabel">Tiba kembali di</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['returnArrivalAt'] ?? '' }}</td></tr>
                    <tr><td class="rlabel">Pada Tanggal</td><td class="rsep">:</td><td class="rval">{{ $travelOrder['returnArrivalDate'] ?? '' }}</td></tr>
                </table>
                <p class="verif">
                    Telah diperiksa dengan keterangan bahwa perjalanan tersebut diatas benar dilakukan
                    atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang
                    sesingkat-singkatnya.
                </p>
            </td>
        </tr>
    </table>

@endsection