{{--
    Blok wilayah "Diisi Oleh Petugas" (Provinsi / Kab / Kecamatan / Desa + Dusun).
    Dipakai: relocation-cover-letter, relocation-certificate-form, resident-arrival-form.

    Urutan sumber nilai (yang pertama terisi dipakai):
      1. $form  -> province_code / province_name, regency_code / regency_name,
                   district_code / district_name, village_code / village_name  (hasil isian/edit petugas di admin)
      2. $region -> dari LetterPdfService (kode berupa array digit)
      3. DEFAULT -> Kalurahan Bimomartani, Ngemplak, Sleman, DIY

    Dusun: nama dari form (hamlet / originHamlet / origin.hamlet), kode dari hamletCode / hamlet_code (opsional, tanpa default).
--}}
@php
    $rgForm   = $form ?? [];
    $rgRegion = $region ?? [];

    $rgDefault = [
        'province' => ['code' => '34',  'name' => 'DAERAH ISTIMEWA YOGYAKARTA'],
        'regency'  => ['code' => '04',  'name' => 'SLEMAN'],
        'district' => ['code' => '11',  'name' => 'NGEMPLAK'],
        'village'  => ['code' => '020', 'name' => 'BIMOMARTANI'],
    ];

    // Nilai pertama yang TERISI (string kosong / spasi dilewati, bukan menghentikan pencarian seperti operator ??)
    $rgFirst = function (array $candidates) {
        foreach ($candidates as $v) {
            if (is_array($v)) { continue; }
            $v = trim((string) $v);
            if ($v !== '') { return $v; }
        }
        return '';
    };

    // Kode -> array digit (terima string "34" maupun array ['3','4']).
    $rgDigits = function (string $k) use ($rgForm, $rgRegion, $rgDefault) {
        foreach ([$rgForm[$k . '_code'] ?? null, $rgForm[$k . 'Code'] ?? null, $rgRegion[$k . '_code'] ?? null] as $v) {
            if (is_array($v)) { $v = implode('', $v); }
            $v = preg_replace('/\s+/', '', (string) $v);
            if ($v !== '') { return str_split($v); }
        }
        return str_split($rgDefault[$k]['code']);
    };

    $rgName = function (string $k) use ($rgForm, $rgRegion, $rgDefault) {
        foreach ([$rgForm[$k . '_name'] ?? null, $rgForm[$k . 'Name'] ?? null, $rgRegion[$k . '_name'] ?? null] as $v) {
            $v = trim((string) $v);
            if ($v !== '') { return mb_strtoupper($v); }
        }
        return $rgDefault[$k]['name'];
    };

    $rgRows = [
        'province' => 'PROVINSI',
        'regency'  => 'KABUPATEN / KOTA',
        'district' => 'KECAMATAN',
        'village'  => 'DESA / KELURAHAN',
    ];

    // Dusun: key 'hamlet' (surat datang) atau originHamlet / origin_hamlet / origin.hamlet (surat pindah).
    $rgHamlet = $rgFirst([
        $hamletValue ?? null,
        $rgForm['hamlet'] ?? null,
        $rgForm['originHamlet'] ?? null,
        $rgForm['origin_hamlet'] ?? null,
        $rgForm['origin']['hamlet'] ?? null,
    ]);
    // Kode dusun (isian petugas) -> hanya angka; kosong = baris dusun tanpa kotak kode
    $rgHamletCode = preg_replace('/\D/', '', $rgFirst([
        $rgForm['hamlet_code'] ?? null,
        $rgForm['hamletCode'] ?? null,
        $rgForm['origin']['hamlet_code'] ?? null,
    ]));
@endphp
<table class="wgrid">
    @foreach ($rgRows as $rgKey => $rgLabel)
    <tr>
        <td class="wg-label">{{ $rgLabel }}</td>
        <td class="wg-sep">:</td>
        <td>
            <table class="wg-kode"><tr>
                @foreach ($rgDigits($rgKey) as $d)
                    <td>{{ $d }}</td>
                @endforeach
            </tr></table>
        </td>
        <td class="wg-val"><span class="wg-star">*)</span> {{ $rgName($rgKey) }}</td>
    </tr>
    @endforeach
    @if($showHamlet ?? false)
    <tr>
        <td class="wg-label">DUSUN/DUKUH/KAMPUNG</td>
        <td class="wg-sep">:</td>
        @if ($rgHamletCode !== '')
            <td>
                <table class="wg-kode"><tr>
                    @foreach (str_split($rgHamletCode) as $d)
                        <td>{{ $d }}</td>
                    @endforeach
                </tr></table>
            </td>
            <td class="wg-val">{{ $rgHamlet }}</td>
        @else
            <td colspan="2" class="wg-val">{{ $rgHamlet }}</td>
        @endif
    </tr>
    @endif
</table>