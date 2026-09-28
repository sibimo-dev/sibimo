@extends('letters.marriage.layout')
@section('content')
    @foreach ($letters as $letter)
        <div @class(['page-break' => ! $loop->first])>
            @include('letters.marriage.letters.' . $letter)
        </div>
    @endforeach
@endsection