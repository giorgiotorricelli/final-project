@use('Illuminate\Support\Facades\Storage')

@props(['movie'])

<div class="img-wrapper">
    <img class="poster-img" src="{{ $movie->poster_path ? Storage::url($movie->poster_path) : 'https://placehold.co/400x600/c99aff/000/png?text=Poster+:)' }}" alt="{{ $movie->title }} poster">
</div>
<div class="card-body">
    <h5 class="movie-title">{{$movie->title}}</h5>
</div>