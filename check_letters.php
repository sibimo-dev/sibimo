<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdfService = app(App\Services\LetterPdfService::class);
$deathService = app(App\Services\DeathTemplateService::class);

$types = App\Models\LetterType::all();
$missingViews = [];
foreach ($types as $t) {
    $code = strtoupper($t->code);
    $blade = $t->blade_view;
    if ($code === 'SKKM' || str_starts_with($blade, 'letters.death.')) {
        $tmpl = $code === 'SKKM' ? 'death-certificate' : str($blade)->afterLast('.')->toString();
        $has = $deathService->hasTemplate($tmpl);
        echo sprintf("[%s] DEATH tmpl: %s (exists: %s)\n", $code, $tmpl, $has ? 'YES' : 'NO');
        if (!$has) $missingViews[] = "$code (death template $tmpl)";
    } else {
        $root = $pdfService->rootTemplateForType($t);
        $view = $root ? $pdfService->viewName($root) : 'NONE';
        $viewExists = $view !== 'NONE' && view()->exists($view);
        echo sprintf("[%s] ROOT: %s | VIEW: %s (exists: %s)\n", $code, $root ?? 'NULL', $view, $viewExists ? 'YES' : 'NO');
        if (!$viewExists) $missingViews[] = "$code (view $view)";
    }
}

echo "\nSummary of missing views: " . count($missingViews) . "\n";
foreach ($missingViews as $mv) echo " - $mv\n";
