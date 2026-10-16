<?php

namespace Database\Seeders;

use App\Models\FooterSetting;
use Illuminate\Database\Seeder;

class FooterSettingSeeder extends Seeder
{
    public function run(): void
    {
        FooterSetting::query()->updateOrCreate(
            ['footer_setting_id' => 1],
            [
                'village_name' => 'Kalurahan Bimomartani',
                'description' => 'Website resmi Pemerintah Kalurahan Bimomartani, Kapanewon Ngemplak, Kabupaten Sleman, Provinsi Daerah Istimewa Yogyakarta.',
                'social_media' => [
                    ['platform' => 'Facebook',  'url' => 'https://www.facebook.com/'],
                    ['platform' => 'Instagram', 'url' => 'https://www.instagram.com/'],
                    ['platform' => 'YouTube',   'url' => 'https://www.youtube.com/'],
                    ['platform' => 'WhatsApp',  'url' => 'https://wa.me/6281234567890'],
                ],
                'address' => 'Dusun Krajan, Bimomartani, Ngemplak, Sleman, D.I. Yogyakarta, 55584.',
                'phone' => '0812-3456-7890',
                'email' => 'desa.bimomartani@slemankab.go.id',
            ],
        );
    }
}
