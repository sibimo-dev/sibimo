{{-- Masa berlaku KTP: tanggal (YYYY-MM-DD / sudah terformat) atau "Seumur Hidup".
     Pakai: @include('letters.partials.ktp-valid', ['value' => $row['valid_until'] ?? '']) --}}
@php
    $ktpValid = trim((string) ($value ?? ''));
    if (preg_match('/^seumur\s*hidup$/i', $ktpValid)) {
        $ktpValid = 'SEUMUR HIDUP';
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $ktpValid)) {
        $ktpValid = date('d-m-Y', strtotime(substr($ktpValid, 0, 10)));
    }
@endphp
{{ $ktpValid }}
