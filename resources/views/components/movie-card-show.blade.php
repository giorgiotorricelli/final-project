@props(['movie'])

@php
    $cleanDesc = str_replace('—', ', ', str_replace(' -', ', ', $movie->description));
@endphp

<div class="card-show-wrapper w-100">
    <div class="img-show-wrapper">
        <img class="poster-show-img img-fluid" 
             src="{{ $movie->poster_path ? Storage::url($movie->poster_path) : 'https://placehold.co/400x600/c99aff/000/png?text=Poster+:)' }}" 
             alt="{{ $movie->title }} poster"> 
    </div>
    <div class="card-show-body montserrat d-flex flex-column justify-content-between  w-100 position-relative">
        <div class=" position-absolute" style="right: 0">
                <a href="{{ route('admin.movies.edit', $movie->slug) }}" class="btn btn-success"><i class=" bi bi-pencil-square"></i></a>
                <!-- button trigger modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class=" bi bi-trash"></i>
                </button>
            </div>
        <div>
            <h3>{{ $movie->title }}</h3>
            <p>{{ $cleanDesc }}</p>
            <div>
                @foreach ($movie->genres as $genre)
                    <span class="tag" style="background-color: {{ $genre->color }}80; ">{{ $genre->name }}</span>
                @endforeach
            </div>
        </div>
        <div class=" d-flex align-items-center justify-content-between">
            <div>
                <p class=" d-inline m-0">Rating:</p>
                @for ($i = 0; $i < 10; $i++)
                    @if ($movie->rating - $i < 0.75 && $movie->rating - $i > 0.25)
                        <i class="bi bi-star-half"></i>
                    @elseif ($i < $movie->rating)
                        <i class="bi bi-star-fill"></i>
                    @else
                        <i class="bi bi-star"></i>
                    @endif
                @endfor
                
            </div>
            <p class=" d-inline m-0">
                {{ $movie->release_date }}
            </p>
        </div>
    </div>
</div>


