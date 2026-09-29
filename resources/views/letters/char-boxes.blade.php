{{-- Reusable single-character boxes, meant to sit inline as <td> cells inside
     an existing table row (does NOT open its own <table>, to avoid nested
     borders / "double box" look). Used for Nama Lengkap, No KK, NIK. --}}
@php
    $chars = str_split(strtoupper((string) ($value ?? '')));
    $chars = array_pad($chars, $length, '');
    $chars = array_slice($chars, 0, $length);
@endphp
@foreach ($chars as $char)
    <td class="char-cell">{{ $char }}</td>
@endforeach