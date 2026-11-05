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
        <div class=" flex-grow-1">
            <h3>{{ $movie->title }}</h3>
            <p>{{ $cleanDesc }}</p>
            <div>
                @foreach ($movie->genres as $genre)
                    <span class="tag" style="background-color: {{ $genre->color }}80; ">{{ $genre->name }}</span>
                @endforeach
            </div>
            <div class="d-flex justify-content-between align-items-start gap-4 mt-3">
                
                {{-- Sezione Actors --}}
                @if(isset($movie->actors) && $movie->actors->count() > 0)
                    <div class="flex-grow-1">
                        <h5 class="m-0">Actors</h5>
                        <div class="d-flex gap-3 mt-2 flex-wrap">
                            @foreach ($movie->actors as $actor)
                                <a href="{{ route('admin.actors.show', $actor->id) }}" class="text-decoration-none">
                                    <div class="movie-show-actor-img-wrapper" title="{{ $actor->name }}">
                                        <img class="movie-show-actor-img" src="{{ $actor->profile_path ? Storage::url($actor->profile_path) : 'https://placehold.co/100x150/481c7a/fff/png?text=Actor' }}" alt="{{ $actor->name }}">
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Sezione Director (Tutto a destra) --}}
                @if(isset($movie->director))
                    <div class="text-end">
                        <h5 class="m-0">Director</h5>
                        <div class="d-flex justify-content-end mt-2">
                            <a href="{{ route('admin.directors.show', $movie->director->id) }}" class="text-decoration-none">
                                <div class="movie-show-actor-img-wrapper" title="{{ $movie->director->name }}">
                                    <img class="movie-show-actor-img" src="{{ $movie->director->profile_path ? Storage::url($movie->director->profile_path) : 'https://placehold.co/100x150/481c7a/fff/png?text=Director' }}" alt="{{ $movie->director->name }}">
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
                </div>
        </div>
        <div class="d-flex align-items-center justify-content-between ">
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


