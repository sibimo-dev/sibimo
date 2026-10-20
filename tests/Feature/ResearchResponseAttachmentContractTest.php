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

it('accepts the research response form attachment labels for its SBP type', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);
    $this->seed(LetterTypeDocumentSeeder::class);
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id'),
    ]);
    $letterType = LetterType::with('documents')->where('code', 'SBP')->firstOrFail();
    Storage::fake('public');

    $labels = [
        'Surat pengantar penelitian dari instansi / perguruan tinggi',
        'Proposal / rencana penelitian',
        'Fotokopi/foto KTP pemohon',
    ];

    foreach ($labels as $label) {
        expect($letterType->documents->firstWhere('document_name', $label))->not->toBeNull();
    }

    $response = $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => array_map(fn (string $label, int $index) => [
            'label' => $label,
            'file' => UploadedFile::fake()->image("dokumen-{$index}.png"),
        ], $labels, array_keys($labels)),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.letter_type.code', 'SBP')
        ->assertJsonCount(3, 'data.attachments');

    foreach ($labels as $index => $label) {
        $response->assertJsonPath("data.attachments.{$index}.letter_type_document.document_name", $label);
    }
});

it('keeps rejecting research response attachment labels outside its configured SBP documents', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);
    $this->seed(LetterTypeDocumentSeeder::class);
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id'),
    ]);
    $letterType = LetterType::where('code', 'SBP')->firstOrFail();
    Storage::fake('public');

    $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $letterType->letter_type_id,
        'applicant_name' => 'Pemohon Uji',
        'applicant_nik' => '3400000000000001',
        'applicant_address' => 'Alamat Uji',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
        'attachments' => [[
            'label' => 'Label tidak dikenal',
            'file' => UploadedFile::fake()->image('unknown.png'),
        ]],
    ])->assertUnprocessable()->assertJsonValidationErrors(['attachments']);
});

it('accepts an SBP request without resident NIK or address but keeps both requirements for other types', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);
    $this->seed(LetterTypeDocumentSeeder::class);
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id'),
    ]);
    $sbp = LetterType::where('code', 'SBP')->firstOrFail();
    $spskg = LetterType::where('code', 'SPSKG')->firstOrFail();

    $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $sbp->letter_type_id,
        'applicant_name' => 'Peneliti Sintetis',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
    ])->assertCreated()
        ->assertJsonPath('data.letter_type.code', 'SBP')
        ->assertJsonPath('data.applicant_name', 'Peneliti Sintetis')
        ->assertJsonPath('data.applicant_nik', '')
        ->assertJsonPath('data.applicant_address', null);

    $this->actingAs($user, 'sanctum')->post('/api/letter-requests', [
        'letter_type_id' => $spskg->letter_type_id,
        'applicant_name' => 'Pemohon Sintetis',
        'source' => 'Manual (Kelurahan)',
        'form_data' => [],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['applicant_nik', 'applicant_address']);
});

it('keeps legacy SBP document rows for existing references but makes them optional', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);

    $typeId = LetterType::where('code', 'SBP')->value('letter_type_id');
    foreach (['Fotocopy Kartu Keluarga (KK)', 'Fotocopy Kartu Tanda Penduduk (KTP)'] as $name) {
        DB::table('letter_type_documents')->updateOrInsert([
            'letter_type_id' => $typeId,
            'document_name' => $name,
        ], ['is_required' => true, 'created_at' => now()]);
    }

    $this->seed(LetterTypeDocumentSeeder::class);

    expect(DB::table('letter_type_documents')
        ->where('letter_type_id', $typeId)
        ->whereIn('document_name', ['Fotocopy Kartu Keluarga (KK)', 'Fotocopy Kartu Tanda Penduduk (KTP)'])
        ->where('is_required', true)
        ->count())->toBe(0)
        ->and(DB::table('letter_type_documents')->where('letter_type_id', $typeId)->count())->toBe(5);
});
