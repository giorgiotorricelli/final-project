@props(['actor'])

@use('Illuminate\Support\Facades\Storage')

<div class="card-show-wrapper w-100">
    <div class="img-show-wrapper">
        <img class="poster-show-img img-fluid" 
             src="{{ $actor->profile_path ? Storage::url($actor->profile_path) : 'https://placehold.co/400x600/c99aff/000/png?text=Profile+:)' }}" 
             alt="{{ $actor->name }} profile photo"> 
    </div>
    <div class="card-show-body montserrat d-flex flex-column justify-content-between w-100 position-relative">
        <div class="position-absolute" style="right: 0">
            <a href="{{ route('admin.actors.edit', $actor->id) }}" class="btn btn-success"><i class="bi bi-pencil-square"></i></a>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div>
            <h3>{{ $actor->name }}</h3>
            
            <!-- Sezione Biografia con Scrollbar -->
            <div class="actor-biography-scroll my-2">
                @if($actor->biography)
                    <p class="small m-0">{{ $actor->biography }}</p>
                @else
                    <p class=" small m-0">No biography available.</p>
                @endif
            </div>

            <div class="mb-3">
                <span class="badge bg-secondary">Popularity: {{ number_format($actor->popularity, 2)  }}</span>
                @if($actor->birthday)
                    <span class="badge bg-info text-dark">Birthday: {{ $actor->birthday }}</span>
                @endif
                @if($actor->deathday)
                    <span class="badge bg-dark">Deathday: {{ $actor->deathday }}</span>
                @endif
            </div>

            @if($actor->place_of_birth)
                <p class="mb-2 small"><strong>Place of Birth:</strong> {{ $actor->place_of_birth }}</p>
            @endif

            {{-- Sezione film collegati all'attore (se presenti) --}}
            @if(isset($actor->movies) && $actor->movies->count() > 0)
                <h5 class="mt-3">Movies</h5>
                <div class="d-flex gap-3 mt-2 flex-wrap">
                    @foreach ($actor->movies as $movie)
                        <a href="{{ route('admin.movies.show', $movie->slug) }}" class="text-decoration-none">
                            <div class="movie-show-actor-img-wrapper" title="{{ $movie->title }}">
                                <img class="movie-show-actor-img" src="{{ $movie->poster_path ? Storage::url($movie->poster_path) : 'https://placehold.co/100x150/481c7a/fff/png?text=Movie' }}" alt="{{ $movie->title }}">
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-between mt-3">
            <div>
                <p class="d-inline m-0 text-muted">Actor ID: {{ $actor->id }}</p>
            </div>
            @if($actor->birthday)
                <p class="d-inline m-0 text-muted">
                    Born: {{ $actor->birthday }}
                </p>
            @endif
        </div>
    </div>
</div>