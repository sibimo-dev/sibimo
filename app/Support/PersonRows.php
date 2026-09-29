<?php

namespace App\Support;

class PersonRows
{
    public static function make(
        array $p,
        string|false $bin = 'Bin',
        bool $gender = false,
        ?string $education = null,
        string $nameLabel = 'Nama lengkap dan alias',
    ): array {
        $rows = [$nameLabel => $p['name']];

        if ($bin !== false) { $rows[$bin] = $p['bin']; }
        $rows['NIK'] = $p['nik'];
        if ($gender) { $rows['Jenis Kelamin'] = $p['gender']; }

        $rows['Tempat dan tanggal lahir'] = $p['birth'];
        $rows['Kewarganegaraan']          = $p['citizenship'];
        $rows['Agama']                    = $p['religion'];
        $rows['Pekerjaan']                = $p['occupation'];
        if ($education === 'before') { $rows['Pendidikan Terakhir'] = $p['education']; }
        $rows['Alamat']                   = $p['address'];
        if ($education === 'after')  { $rows['Pendidikan Terakhir'] = $p['education']; }

        return $rows;
    }
}