@use('Illuminate\Support\Facades\Storage')

@props(['movie'])

<div class="img-wrapper">
    <img class="poster-img" src="{{ Storage::url($movie->poster_path) }}" alt="{{ $movie->title }} poster">
</div>
<div class="card-body">
    <h5 class="movie-title">{{$movie->title}}</h5>
</div>