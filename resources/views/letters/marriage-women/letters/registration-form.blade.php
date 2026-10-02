@php
    $m = $marriage;
    $person = fn ($p, $bin = 'BIN') => [
        'Nama'                 => $p['name'],
        $bin                   => $p['bin'],
        'NIK'                  => $p['nik'],
        'Tempat Tanggal Lahir' => $p['birth'],
        'Kewarganegaraan'      => $p['citizenship'],
        'Agama'                => $p['religion'],
        'Pekerjaan'            => $p['occupation'],
        'Alamat'               => $p['address'],
    ];
@endphp
<div class="title" style="text-align:center;font-weight:bold;font-size:10pt;margin:0 0 14pt 0">
    DATA ISIAN PENDAFTARAN NIKAH
</div>

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA DESA',
    'upper' => false,
    'rows'  => [
        'DESA'                       => $region['village_name'],
        'KECAMATAN'                  => $region['district_name'],
        'KABUPATEN/KOTA'             => $region['regency_name'],
        'NO. SURAT'                  => $m['letter_number'],
        'TANGGAL SURAT'              => $signature['date_long'],
        'NAMA KEPALA DESA/LURAH/An.' => $signer['name'],
    ],
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA AKAD NIKAH',
    'rows'  => [
        'HARI AKAD'    => $m['akad']['day'],
        'TANGGAL AKAD' => $m['akad']['date'],
        'JAM AKAD'     => $m['akad']['time'],
        'TEMPAT AKAD'  => $m['akad']['place'],
    ],
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA CATIN PUTRI',
    'rows'  => [
        'Nama'                 => $m['bride']['name'],
        'NIK'                  => $m['bride']['nik'],
        'Tempat Tanggal Lahir' => $m['bride']['birth'],
        'Kewarganegaraan'      => $m['bride']['citizenship'],
        'Agama'                => $m['bride']['religion'],
        'Pekerjaan'            => $m['bride']['occupation'],
        'Pendidikan Terakhir'  => $m['bride']['education'],
        'Alamat'               => $m['bride']['address'],
        'Status'               => $m['bride']['status'],
        'Nama suami terdahulu'                  => $m['ex_husband']['name'],
        'Bin suami terdahulu/nama orangtua'     => $m['ex_husband']['bin'],
        'NIK suami terdahulu'                   => $m['ex_husband']['nik'],
        'TTL suami terdahulu'                   => $m['ex_husband']['birth'],
        'Kewarganegaraan suami terdahulu'       => $m['ex_husband']['citizenship'],
        'Agama suami terdahulu'                 => $m['ex_husband']['religion'],
        'Pekerjaan suami terdahulu'              => $m['ex_husband']['occupation'],
        'Alamat suami terdahulu'                 => $m['ex_husband']['address'],
        'Meninggal dunia pada'                   => $m['ex_husband']['died_at'],
        'Meninggal di'                            => $m['ex_husband']['died_place'],
    ],
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA AYAH CATIN PUTRI',
    'rows'  => $person($m['bride_father'], 'BIN'),
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA IBU CATIN PUTRI',
    'rows'  => $person($m['bride_mother'], 'BINTI'),
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA WALI NIKAH (Diisi jika wali NASAB BUKAN AYAH KANDUNG)',
    'rows'  => $person($m['guardian']) + [
        'Hubungan wali'                     => $m['guardian']['relation'],
        'Sebab wali bukan ayah kandung'      => $m['guardian']['reason'],
        'JIKA WALI HAKIM, sebab wali hakim'  => $m['judge_guardian_reason'],
    ],
])

@include('letters.marriage-women.partials.data-block', [
    'title' => 'DATA CATIN PUTRA',
    'rows'  => $person($m['groom']),
])