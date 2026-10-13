<?php

use App\Services\LetterPdfService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/letters/preview/{template}', function (string $template, LetterPdfService $service) {
    $data = $service->sampleViewData($template);

    return $service->previewHtml("letters.{$template}", $data);
});

Route::get('/letters/pdf/{template}', function (string $template, LetterPdfService $service) {
    return $service->pdf("letters.{$template}", $service->sampleViewData($template))
        ->stream("{$template}.pdf");
});