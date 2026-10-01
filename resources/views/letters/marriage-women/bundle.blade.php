@extends('letters.marriage-women.layout')

@section('title', 'Berkas Pernikahan')

@section('content')
    @foreach ($letters as $letter)
        <div @class(['page-break' => ! $loop->first])>
            @include('letters.marriage-women.letters.' . $letter)
        </div>
    @endforeach
@endsection