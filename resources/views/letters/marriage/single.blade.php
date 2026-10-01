@extends('letters.marriage.layout')
@use('App\Services\LetterPdfService')
@section('title', LetterPdfService::MARRIAGE_LETTERS[$letter] ?? 'Surat Pernikahan')
@section('content')
    @include('letters.marriage.letters.' . $letter)
@endsection