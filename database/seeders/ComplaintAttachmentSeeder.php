<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplaintAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $complaints = DB::table('complaints')
            ->where('title', 'like', 'Complaint Seeder %')
            ->get(['complaint_id', 'title']);
        $sourceImages = Storage::disk('public')->files('galleries');

        foreach ($complaints as $complaint) {
            $index = (int) preg_replace('/\D+/', '', $complaint->title);

            if ($index % 2 === 1) {
                $extension = 'jpg';
                $targetPath = 'complaints/seed-complaint-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT) . '.' . $extension;

                if ($sourceImages) {
                    $sourcePath = $sourceImages[($index - 1) % count($sourceImages)];
                    $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION)) ?: 'jpg';
                    $targetPath = 'complaints/seed-complaint-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT) . '.' . $extension;
                    Storage::disk('public')->copy($sourcePath, $targetPath);
                } else {
                    Storage::disk('public')->put($targetPath, $this->fallbackImage());
                }

                $fileName = basename($targetPath);
                DB::table('complaint_attachments')->updateOrInsert(
                    [
                        'complaint_id' => $complaint->complaint_id,
                        'file_name' => $fileName,
                    ],
                    [
                        'file_path' => Storage::disk('public')->url($targetPath),
                        'uploaded_at' => now(),
                    ],
                );
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
