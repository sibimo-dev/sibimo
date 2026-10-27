{{-- ===== CSS KOP (dipakai bersama oleh semua layout/surat yang menampilkan kop) =====
     Di-include di dalam <style> lewat: @include('letters.partials.kop-styles')
     Ukuran sudah diperbesar dibanding versi lama supaya tetap terbaca saat dicetak,
     dengan jarak antarbaris yang lebih longgar (tidak lagi mepet antar teks). --}}
table.kop { width: 100%; border-collapse: collapse; }
table.kop td { padding: 0; vertical-align: top; }
table.kop td.kop-logo { width: 90pt; padding-top: 2pt; }
td.kop-logo img { width: 74pt; height: 96pt; }
table.kop td.kop-text { text-align: center; padding-top: 3pt; }
.kop-1 { font-size: 12.5pt; font-weight: bold; line-height: 14pt; }
.kop-2 { font-size: 14.5pt; font-weight: bold; line-height: 18pt; }
.kop-3 { font-size: 17pt; font-weight: bold; line-height: 21pt; }
.kop-aksara { margin-top: 2pt; margin-bottom: 3pt; }
.kop-aksara img { width: 215pt; height: auto; }
.kop-aksara.lurah img { width: 150pt; }
.kop-info { font-size: 10.6pt; font-weight: bold; line-height: 13.5pt; }
.kop-email { font-size: 10pt; font-weight: bold; line-height: 13pt; }
.kop-line { border-bottom: 2pt solid #000; margin-top: 8pt; }

/* ===== KOP DUKCAPIL (tidak diubah) ===== */
table.kop-dukcapil td.kop-logo { width: 78pt; vertical-align: middle; }
table.kop-dukcapil td.kop-logo img { width: 66pt; height: 86pt; }
table.kop-dukcapil td.kop-text { vertical-align: middle; padding-left: 10pt; }
.kopd-1 { font-size: 12.5pt; font-weight: bold; line-height: 15pt; }
.kopd-2 { font-size: 14.5pt; font-weight: bold; line-height: 18pt; }
.kopd-info { font-size: 9pt; font-weight: bold; line-height: 1.25; }
.kop-line.dukcapil { margin-top: 6pt; }
