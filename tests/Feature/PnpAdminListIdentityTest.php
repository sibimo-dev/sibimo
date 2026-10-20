<?php

use App\Models\LetterRequest;
use App\Models\LetterType;
use App\Models\User;
use Database\Seeders\LetterTypeDocumentSeeder;
use Database\Seeders\LetterTypeFieldSeeder;
use Database\Seeders\LetterTypeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SignerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function pnpAdminListTestUser(): User
{
    $user = User::factory()->create();
    $permissionId = DB::table('permissions')->where('slug', 'pengelolaan-surat')->value('permission_id');
    DB::table('user_permissions')->insert([
        'user_id' => $user->user_id,
        'permission_id' => $permissionId,
    ]);

    return $user;
}

it('returns bride display identity to admin lists without returning the full form payload', function () {
    $this->seed(SignerSeeder::class);
    $this->seed(LetterTypeSeeder::class);
    $this->seed(LetterTypeFieldSeeder::class);
    $this->seed(LetterTypeDocumentSeeder::class);
    $this->seed(PermissionSeeder::class);

    $type = LetterType::where('code', 'PNP')->firstOrFail();
    $request = LetterRequest::create([
        'request_code' => 'TEST-PNP-ADMIN-LIST',
        'applicant_name' => 'Metadata Groom Test',
        'applicant_nik' => '1212121212121212',
        'applicant_phone' => '081222222222',
        'applicant_address' => 'Alamat metadata groom',
        'letter_type_id' => $type->letter_type_id,
        'status' => 'submitted',
        'source' => 'Online',
        'signature_type' => 'digital',
        'submitted_at' => now(),
        'form_data' => [
            'groom_name' => 'Nama groom dummy',
            'groom_nik' => '1212121212121212',
            'bride_name' => 'Nama bride dummy',
            'bride_nik' => '3434343434343434',
            'bride_address' => 'Alamat bride dummy',
            'whatsapp' => '081222222222',
        ],
    ]);

    $response = $this->actingAs(pnpAdminListTestUser(), 'sanctum')
        ->getJson('/api/letter-requests')
        ->assertOk()
        ->assertJsonPath('data.0.letter_type.code', 'PNP')
        ->assertJsonPath('data.0.applicant_display.name', 'Nama bride dummy')
        ->assertJsonPath('data.0.applicant_display.nik', '3434343434343434')
        ->assertJsonPath('data.0.applicant_display.address', 'Alamat bride dummy');

    expect($response->json('data.0.form_data'))->toBeNull()
        ->and(LetterRequest::findOrFail($request->letter_request_id)->applicant_name)->toBe('Metadata Groom Test');
});
