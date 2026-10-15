<?php
$results = json_decode(file_get_contents('preview_audit_results.json'), true);

echo "=== RENDER ERRORS ===\n";
foreach ($results as $r) {
    if ($r['status'] === 'RENDER_ERROR') {
        echo sprintf("- %s (%s): %s\n", $r['slug'], $r['code'] ?? 'N/A', $r['error']);
    }
}

echo "\n=== NO BACKEND CODE ===\n";
foreach ($results as $r) {
    if ($r['status'] === 'NO_BACKEND_CODE') {
        echo sprintf("- %s: %s\n", $r['slug'], $r['title']);
    }
}

echo "\n=== ALL MISSING FIELDS BREAKDOWN ===\n";
$totalMissingCount = 0;
foreach ($results as $r) {
    if ($r['status'] === 'HAS_MISSING') {
        $totalMissingCount += $r['missing_count'];
        echo sprintf("[%s - %s] %s: %d/%d missing\n", $r['code'], $r['slug'], $r['title'], $r['missing_count'], $r['total_fields']);
        echo "   -> " . implode(', ', $r['missing_fields']) . "\n";
    }
}
echo "\nTotal missing fields across all letters: $totalMissingCount\n";
