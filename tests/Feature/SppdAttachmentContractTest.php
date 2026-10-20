<?php

use App\Models\LetterType;
use App\Models\User;
use Database\Seeders\LetterTypeDocumentSeeder;
use Database\Seeders\LetterTypeFieldSeeder;
use Database\Seeders\LetterTypeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SignerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('accepts SPPD attachments using the configured KK and KTP document labels', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);
    $this->seed(LetterTypeDocumentSeeder::class);
    $this->seed(PermissionSeeder::class);
    Storage::fake('public');

    $user = User::factory()->create();
    $permissionId = DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id');
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => $permissionId,
    ]);

    $letterType = LetterType::with('documents')->where('code', 'SPPD')->firstOrFail();
    $documents = $letterType->documents->keyBy('document_name');
    expect($documents->has('Fotocopy Kartu Keluarga (KK)'))->toBeTrue()
        ->and($documents->has('Fotocopy Kartu Tanda Penduduk (KTP)'))->toBeTrue();

    $response = $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [
            [
                'label' => 'Fotocopy Kartu Keluarga (KK)',
                'file' => UploadedFile::fake()->image('kk.png'),
            ],
            [
                'label' => 'Fotocopy Kartu Tanda Penduduk (KTP)',
                'file' => UploadedFile::fake()->image('ktp.png'),
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.letter_type.code', 'SPPD')
        ->assertJsonCount(2, 'data.attachments')
        ->assertJsonPath('data.attachments.0.letter_type_document.document_name', 'Fotocopy Kartu Keluarga (KK)')
        ->assertJsonPath('data.attachments.1.letter_type_document.document_name', 'Fotocopy Kartu Tanda Penduduk (KTP)');

    $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [[
            'label' => 'Surat undangan/pemberitahuan kegiatan dari instansi tujuan',
            'file' => UploadedFile::fake()->image('undangan.png'),
        ]],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['attachments']);
});
