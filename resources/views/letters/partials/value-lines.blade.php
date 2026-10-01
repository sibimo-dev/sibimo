@php
    $rawValue = trim((string) ($value ?? ''));
    $maxChars = (int) ($maxChars ?? 40);
    $lines = [];

    foreach (preg_split('/\R/u', $rawValue) ?: [''] as $paragraph) {
        $paragraph = trim($paragraph);
        $wrapped = $paragraph === ''
            ? ''
            : wordwrap($paragraph, $maxChars, "\n", true);

        $lines = array_merge($lines, $wrapped === '' ? [''] : explode("\n", $wrapped));
    }

    $lines = $lines ?: [''];
@endphp

<div class="value-lines">
    @foreach ($lines as $line)
        <div class="value-line">{{ $line !== '' ? $line : ' ' }}</div>
    @endforeach
</div>
