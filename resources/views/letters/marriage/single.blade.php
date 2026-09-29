@extends('letters.marriage.layout')
@section('content')
    @include('letters.marriage.letters.' . $letter)
@endsection