<table class="wgrid">
    <tr>
        <td class="wg-label">PROVINSI</td>
        <td class="wg-sep">:</td>
        <td>
            <table class="wg-kode"><tr>
                @foreach ($region['province_code'] ?? [] as $d)
                    <td>{{ $d }}</td>
                @endforeach
            </tr></table>
        </td>
        <td class="wg-val"><span class="wg-star">*)</span> {{ $region['province_name'] ?? '' }}</td>
    </tr>
    <tr>
        <td class="wg-label">KABUPATEN / KOTA</td>
        <td class="wg-sep">:</td>
        <td>
            <table class="wg-kode"><tr>
                @foreach ($region['regency_code'] ?? [] as $d)
                    <td>{{ $d }}</td>
                @endforeach
            </tr></table>
        </td>
        <td class="wg-val"><span class="wg-star">*)</span> {{ $region['regency_name'] ?? '' }}</td>
    </tr>
    <tr>
        <td class="wg-label">KECAMATAN</td>
        <td class="wg-sep">:</td>
        <td>
            <table class="wg-kode"><tr>
                @foreach ($region['district_code'] ?? [] as $d)
                    <td>{{ $d }}</td>
                @endforeach
            </tr></table>
        </td>
        <td class="wg-val"><span class="wg-star">*)</span> {{ $region['district_name'] ?? '' }}</td>
    </tr>
    <tr>
        <td class="wg-label">DESA / KELURAHAN</td>
        <td class="wg-sep">:</td>
        <td>
            <table class="wg-kode"><tr>
                @foreach ($region['village_code'] ?? [] as $d)
                    <td>{{ $d }}</td>
                @endforeach
            </tr></table>
        </td>
        <td class="wg-val"><span class="wg-star">*)</span> {{ $region['village_name'] ?? '' }}</td>
    </tr>
    @if($showHamlet ?? false)
    <tr>
        <td class="wg-label">DUSUN/DUKUH/KAMPUNG</td>
        <td class="wg-sep">:</td>
        <td colspan="2" class="wg-val">{{ $form['hamlet'] ?? '' }}</td>
    </tr>
    @endif
</table>