@extends('admin.layouts.master')
@use('Illuminate\Support\Facades\Storage')

@section('content')
    
    <div class="backdrop-wrapper" style="--bg-image: url('{{ Storage::url($movie->backdrop_path) }}')">
        <div class="show-container d-flex align-items-center h-100">
            <x-movie-card-show :movie="$movie"/>
        </div>
    </div>
@endsection