<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $product['title'] }}</title>
    <style>
        @page { margin: 34px 48px 42px; }
        body { color: #172033; font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.55; }
        .header { border-bottom: 2px solid #172033; padding-bottom: 10px; text-align: center; }
        .header .government { font-size: 13px; font-weight: bold; letter-spacing: .4px; }
        .header .office { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .header .address { font-size: 9px; margin-top: 4px; }
        .document-meta { margin-top: 22px; text-align: center; }
        .document-meta .category { font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .document-meta .title { font-size: 14px; font-weight: bold; margin: 10px auto 4px; text-transform: uppercase; }
        .document-meta .number { font-size: 11px; }
        .intro { margin-top: 22px; text-align: justify; }
        .section-title { font-weight: bold; margin-top: 16px; text-transform: uppercase; }
        .items { margin: 6px 0 0 18px; padding: 0; }
        .items li { margin-bottom: 5px; padding-left: 4px; }
        .decision { margin-top: 14px; text-align: justify; }
        .signature { margin-top: 34px; margin-left: 58%; text-align: center; }
        .signature .place { margin-bottom: 48px; }
        .signature .name { font-weight: bold; text-decoration: underline; }
        .footer { border-top: 1px solid #bbc3d1; color: #586579; font-size: 8px; margin-top: 38px; padding-top: 6px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="government">PEMERINTAH KABUPATEN SLEMAN</div>
        <div class="office">KAPANEWON NGEMPLAK</div>
        <div class="office">KALURAHAN BIMOMARTANI</div>
        <div class="address">Jl. Prambanan-Cangkringan Km. 6,5, Bimomartani, Ngemplak, Sleman, Daerah Istimewa Yogyakarta</div>
    </div>

    <div class="document-meta">
        <div class="category">{{ $product['category_label'] }}</div>
        <div class="title">{{ $product['title'] }}</div>
        <div class="number">{{ $product['number'] }}</div>
    </div>

    <p class="intro">
        Dengan rahmat Tuhan Yang Maha Esa, Pemerintah Kalurahan Bimomartani menerbitkan produk hukum ini sebagai dokumen resmi dalam penyelenggaraan pemerintahan, pembangunan, dan pelayanan masyarakat di Kalurahan Bimomartani.
    </p>

    <div class="section-title">Menimbang</div>
    <ol class="items" type="a">
        <li>bahwa penyelenggaraan pemerintahan kalurahan perlu dilaksanakan secara tertib, transparan, dan bertanggung jawab;</li>
        <li>bahwa diperlukan dasar hukum yang sesuai dengan kebutuhan masyarakat dan ketentuan peraturan perundang-undangan;</li>
        <li>bahwa berdasarkan pertimbangan tersebut perlu menetapkan {{ strtolower($product['category_label']) }} ini.</li>
    </ol>

    <div class="section-title">Mengingat</div>
    <ol class="items">
        <li>Undang-Undang Nomor 6 Tahun 2014 tentang Desa beserta perubahannya;</li>
        <li>peraturan perundang-undangan yang mengatur penyelenggaraan pemerintahan desa dan kalurahan;</li>
        <li>ketentuan lain yang berkaitan dengan {{ $product['topic'] }}.</li>
    </ol>

    <div class="section-title">Memutuskan</div>
    <p class="decision">
        Menetapkan: <strong>{{ $product['title'] }}</strong> sebagai pedoman pelaksanaan {{ $product['topic'] }} di wilayah Kalurahan Bimomartani.
    </p>
    <p class="decision">
        Produk hukum ini berlaku sejak tanggal ditetapkan dan digunakan sebagaimana mestinya. {{ $product['description'] }}
    </p>

    <div class="signature">
        <div class="place">Bimomartani, {{ $product['year'] }}</div>
        <div>LURAH BIMOMARTANI</div>
        <div class="name">Tutik Wahyuningsih, S.Sos., M.AP</div>
    </div>

    <div class="footer">
        Dokumen contoh untuk pratinjau sistem SIBIMO | Status: {{ $product['status_label'] }}
    </div>
</body>
</html>
