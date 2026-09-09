<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplaintAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $complaintIds = DB::table('complaints')->pluck('complaint_id');
        $sourceImages = Storage::disk('public')->files('galleries');

        foreach ($complaintIds as $complaintId) {
            if (fake()->boolean(60)) {
                $targetPath = 'complaints/' . fake()->uuid() . '.jpg';

                if ($sourceImages) {
                    $sourcePath = fake()->randomElement($sourceImages);
                    $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) ?: 'jpg';
                    $targetPath = 'complaints/' . fake()->uuid() . '.' . $extension;
                    Storage::disk('public')->copy($sourcePath, $targetPath);
                } else {
                    Storage::disk('public')->put($targetPath, $this->fallbackImage());
                }

                $fileName = basename($targetPath);
                DB::table('complaint_attachments')->insert([
                    'complaint_id' => $complaintId,
                    'file_name' => $fileName,
                    'file_path' => Storage::disk('public')->url($targetPath),
                    'uploaded_at' => now(),
                ]);
            }
        }
    }

    private function fallbackImage(): string
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="450" viewBox="0 0 800 450">'
            . '<rect width="800" height="450" fill="#e2e8f0"/>'
            . '<text x="400" y="235" text-anchor="middle" fill="#475569" font-size="28" font-family="Arial">Lampiran Aduan</text>'
            . '</svg>';
    }
}
