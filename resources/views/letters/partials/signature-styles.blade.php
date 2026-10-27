{{-- CSS blok tanda tangan pejabat (dipakai base layout & surat mandiri yang include partials.signature-block) --}}
table.ttd {
    width: 100%;
    border-collapse: collapse;
    page-break-inside: avoid;
}
table.ttd td { padding: 0; vertical-align: top; }
table.ttd td.ttd-spacer { width: 265pt; }
table.ttd td.ttd-content {
    width: auto;
    line-height: 20pt;
    padding-top: 22pt;
}
.ttd-space { height: 52pt; }
.signature-image { display: block; width: auto; max-width: 120pt; max-height: 48pt; margin: 0 auto; object-fit: contain; }
