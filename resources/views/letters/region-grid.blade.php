{{--
    Blok wilayah "Diisi Oleh Petugas" (Provinsi / Kab / Kecamatan / Desa + Dusun).
    Dipakai: relocation-cover-letter, relocation-certificate-form, resident-arrival-form.

    Urutan sumber nilai (yang pertama terisi dipakai):
      1. $form  -> province_code / province_name, regency_code / regency_name,
                   district_code / district_name, village_code / village_name  (hasil isian/edit petugas di admin)
      2. $region -> dari LetterPdfService (kode berupa array digit)
      3. DEFAULT -> Kalurahan Bimomartani, Ngemplak, Sleman, DIY

    Dusun & Nomor KK TIDAK punya default: murni isian petugas.
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

    // Dusun: isian petugas. Key bisa 'hamlet' (surat datang) atau originHamlet (surat pindah).
    $rgHamlet = $hamletValue
        ?? ($rgForm['hamlet'] ?? null)
        ?: ($rgForm['originHamlet'] ?? $rgForm['origin_hamlet'] ?? ($rgForm['origin']['hamlet'] ?? ''));
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
        <td colspan="2" class="wg-val">{{ $rgHamlet }}</td>
    </tr>
    @endif
</table>