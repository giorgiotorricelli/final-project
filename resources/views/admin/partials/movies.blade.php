@use('Illuminate\Support\Facades\File')

@php
    $path_absolute = database_path('data/movies.json');
    //D:\xampp\htdocs\Laravel\final-project\database\data/movies.json

    if(File::exists($path_absolute)) {
        $rawContent = File::get($path_absolute);

        $movies = json_decode($rawContent, true);
    }

    
@endphp

@extends('admin.layouts.master')

@section('content')
    <div>
        <p>sei nella pagina di gestione di tutti i film</p>
        @foreach ($movies as $movie)
            <p>{{$movie['title']}}</p>
        @endforeach
    </div>
@endsection