<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Formulir')</title>
    <style>
        @page { margin: 1cm 1.3cm 1cm 1.3cm; }

body {
    margin: 0;
    font-family: Helvetica, Arial, sans-serif;
    font-size: 9.5pt;
    line-height: 1.15;
    color: #000;
}

.kode-form {
    border: 1px solid #000;
    width: 56pt;
    text-align: center;
    font-weight: bold;
    padding: 3pt 0;
    float: right;
}
.clear { clear: both; }

.judul-form {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
    text-transform: uppercase;
    margin-top: 4pt;
}
.subjudul-form { text-align: center; font-size: 9pt; margin-top: 1pt; }

/* Grid wilayah: label + N kotak kode kecil + 1 kotak nilai panjang */
table.wgrid { width: 100%; border-collapse: collapse; margin-top: 3pt; }
table.wgrid td { padding: 0; vertical-align: middle; }
table.wgrid td.wg-label { width: 150pt; font-size: 9.5pt; padding-right: 0; }
table.wgrid td.wg-sep { width: 10pt; }
table.wgrid table.wg-kode { border-collapse: collapse; }
table.wgrid table.wg-kode td {
    border: 1px solid #000; width: 17pt; height: 12pt;
    text-align: center; font-weight: bold; font-size: 9pt;
}
table.wgrid td.wg-val { border: 1px solid #000; padding-left: 4pt; height: 13pt; }
.wg-star { font-size: 8pt; }

/* Baris label:kotak sederhana, dipakai berulang, lebar disetel per pemakaian */
table.frow { width: 100%; border-collapse: collapse; margin-top: 1.5pt; }
table.frow td { padding: 1pt 0; vertical-align: middle; font-size: 9.5pt; }
table.frow td.f-no { width: 15pt; }
td.f-box { border: 1px solid #000; padding-left: 4pt; height: 12pt; }

/* Kotak digit kecil beruntun (telepon) - ukuran tetap, dipakai bila ruang terbatas */
table.digit-box { border-collapse: collapse; display: inline-table; vertical-align: middle; }
table.digit-box td { border: 1px solid #000; width: 13pt; height: 12pt; }

/* Kotak digit yang melebar mengisi penuh sisa lebar baris (mis. nomor telepon) */
table.digit-box-full { width: 100%; border-collapse: collapse; table-layout: fixed; }
table.digit-box-full td { border: 1px solid #000; height: 12pt; text-align: center; }

.sub-heading { font-weight: bold; margin-top: 4pt; }

.opsi-box {
    border: 1px solid #000; display: inline-block;
    width: 13pt; height: 12pt; text-align: center; font-weight: bold; margin-right: 3pt;
}

table.tbl-bordered { width: 100%; border-collapse: collapse; margin-top: 3pt; }
table.tbl-bordered th, table.tbl-bordered td {
    border: 1px solid #000; padding: 1.5pt 3pt; font-size: 8.7pt; text-align: left;
}
table.tbl-bordered th { text-align: center; font-weight: bold; }

table.ttd-multi { width: 100%; margin-top: 3pt; border-collapse: collapse; }
table.ttd-multi td { text-align: center; vertical-align: top; padding: 0 6pt; font-size: 9.5pt; }
table.ttd-multi .ttd-space { height: 50pt; }

@yield('extra_css')
    </style>
</head>
<body>

    <div class="kode-form">{{ $kodeForm ?? '' }}</div>
    <div class="clear"></div>

    @yield('content')

</body>
</html>