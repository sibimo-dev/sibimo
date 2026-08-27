<?php

namespace Database\Seeders;

use App\Models\Signer;
use Illuminate\Database\Seeder;

class SignerSeeder extends Seeder
{
    public function run(): void
    {
        $signers = [
            ['name' => 'Ahmad Hidayat', 'position' => 'Kepala Desa'],
            ['name' => 'Rasyifa Anom S., AMd.Kes', 'position' => 'Kasi Kesejahteraan'],
            ['name' => 'Siti Aminah', 'position' => 'Kasi Pemerintahan'],
            ['name' => 'Budi Wijaya', 'position' => 'Sekretaris Desa (Carik)'],
        ];

        foreach ($signers as $signer) {
            Signer::create($signer);
        }
    }
}
