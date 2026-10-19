<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdfService = app(App\Services\LetterPdfService::class);
$deathService = app(App\Services\DeathTemplateService::class);

$letters = json_decode(file_get_contents('letter_forms.json'), true);

$results = [];

foreach ($letters as $item) {
    $slug = $item['slug'];
    $code = $item['code'];
    $title = $item['title'];
    $fields = $item['fields'] ?? [];

    if (!$code) {
        $results[] = [
            'slug' => $slug,
            'title' => $title,
            'status' => 'NO_BACKEND_CODE',
            'missing' => [],
        ];
        continue;
    }

    $letterType = App\Models\LetterType::where('code', $code)->first();
    if (!$letterType) {
        $results[] = [
            'slug' => $slug,
            'code' => $code,
            'title' => $title,
            'status' => 'LETTER_TYPE_NOT_FOUND',
            'missing' => [],
        ];
        continue;
    }

    // Build form data
    $formData = [];
    $trackedValues = [];

    foreach ($fields as $f) {
        $key = $f['key'];
        $type = $f['type'] ?? 'text';
        $columns = $f['columns'] ?? null;

        if ($columns) {
            // Table field
            $row = [];
            foreach ($columns as $col) {
                $cKey = $col['key'];
                $val = "TAB_{$key}_{$cKey}";
                $row[$cKey] = $val;
                $trackedValues[$val] = "$key.$cKey ({$col['label']})";
            }
            $formData[$key] = [$row];
            continue;
        }

        if ($type === 'date') {
            $val = '2026-05-20';
            $formData[$key] = $val;
            // Dates can be rendered as 20 Mei 2026, 20/05/2026, 20-05-2026, 2026-05-20, or 20
            $trackedValues['20 Mei 2026'] = "$key ({$f['label']})";
            $trackedValues['20-05-2026'] = "$key ({$f['label']})";
            $trackedValues['2026-05-20'] = "$key ({$f['label']})";
        } elseif ($type === 'time') {
            $val = '11:22';
            $formData[$key] = $val;
            $trackedValues['11:22'] = "$key ({$f['label']})";
        } else {
            $val = "TXT_{$key}_VAL";
            $formData[$key] = $val;
            $trackedValues[$val] = "$key ({$f['label']})";
        }
    }

    // Applicant fallback
    $formData['name'] = $formData['name'] ?? 'TXT_applicant_name_VAL';
    $formData['applicant_name'] = $formData['applicant_name'] ?? 'TXT_applicant_name_VAL';
    $formData['nik'] = $formData['nik'] ?? '3404010101010001';
    $formData['applicant_nik'] = $formData['applicant_nik'] ?? '3404010101010001';
    $formData['address'] = $formData['address'] ?? 'TXT_applicant_address_VAL';
    $formData['applicant_address'] = $formData['applicant_address'] ?? 'TXT_applicant_address_VAL';

    $letterRequest = new App\Models\LetterRequest([
        'letter_type_id' => $letterType->id,
        'applicant_name' => $formData['applicant_name'] ?? 'TXT_applicant_name_VAL',
        'applicant_nik' => $formData['applicant_nik'] ?? '3404010101010001',
        'applicant_phone' => '08123456789',
        'applicant_address' => $formData['applicant_address'] ?? 'TXT_applicant_address_VAL',
        'source' => 'Manual (Kelurahan)',
        'form_data' => $formData,
        'letter_number' => 'TEST/NUM/001',
        'status' => 'authorized',
        'authorized_at' => now(),
    ]);
    $letterRequest->setRelation('letterType', $letterType);

    try {
        $blade = $letterType->blade_view;
        if ($code === 'SKKM' || str_starts_with($blade, 'letters.death.')) {
            $tmpl = $code === 'SKKM' ? 'death-certificate' : str($blade)->afterLast('.')->toString();
            $data = $deathService->dataForRequest($letterRequest, $tmpl);
            $viewName = "letters.death.$tmpl";
        } else {
            $root = $pdfService->rootTemplateForType($letterType);
            $data = $pdfService->viewData($letterRequest, $root);
            $viewName = $pdfService->viewName($root);
        }

        $rendered = view($viewName, $data)->render();

        $missing = [];
        foreach ($trackedValues as $testVal => $fieldDesc) {
            if (stripos($rendered, $testVal) === false) {
                // If it's a date alternative, check if any of the formats were found
                if (in_array($testVal, ['20 Mei 2026', '20-05-2026', '2026-05-20'])) {
                    if (stripos($rendered, '20 Mei 2026') !== false || stripos($rendered, '20-05-2026') !== false || stripos($rendered, '2026-05-20') !== false || stripos($rendered, '20 Mei') !== false || stripos($rendered, '20/05/2026') !== false) {
                        continue;
                    }
                }

                // Check if string is split into individual char boxes (e.g., F-1.01, F-1.02, F-1.25, KTP)
                if (strlen($testVal) <= 30) {
                    $pattern = implode('.*?', array_map(fn($c) => preg_quote($c, '/'), str_split($testVal)));
                    if (preg_match('/' . $pattern . '/is', $rendered)) {
                        continue;
                    }
                }

                $missing[$fieldDesc] = true;
            }
        }

        $results[] = [
            'slug' => $slug,
            'code' => $code,
            'title' => $title,
            'status' => count($missing) === 0 ? 'OK' : 'HAS_MISSING',
            'total_fields' => count($fields),
            'missing_count' => count($missing),
            'missing_fields' => array_keys($missing),
        ];
    } catch (\Throwable $e) {
        $results[] = [
            'slug' => $slug,
            'code' => $code,
            'title' => $title,
            'status' => 'RENDER_ERROR',
            'error' => $e->getMessage(),
        ];
    }
}

file_put_contents('preview_audit_results.json', JSON_encode($results, JSON_PRETTY_PRINT));
$okCount = count(array_filter($results, fn($r) => $r['status'] === 'OK'));
$hasMissing = count(array_filter($results, fn($r) => $r['status'] === 'HAS_MISSING'));
$errCount = count(array_filter($results, fn($r) => $r['status'] === 'RENDER_ERROR'));
echo "Total audited: " . count($results) . "\n";
echo "Fully OK: $okCount\n";
echo "Has Missing Fields: $hasMissing\n";
echo "Render Error: $errCount\n";
