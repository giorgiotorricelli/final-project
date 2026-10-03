@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class="row row-cols-3">
        @foreach ($movieList as $movie)
            <div class="col">
                <x-movie-card :movie="$movie"/>
            </div>
        @endforeach
        </div>
        
    </div>
@endsection