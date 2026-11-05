@props(['director'])

@use('Illuminate\Support\Facades\Storage')

<div class="card-show-wrapper w-100">
    <div class="img-show-wrapper">
        <img class="poster-show-img img-fluid" 
             src="{{ $director->profile_path ? Storage::url($director->profile_path) : 'https://placehold.co/400x600/c99aff/000/png?text=Profile+:)' }}" 
             alt="{{ $director->name }} profile photo"> 
    </div>
    <div class="card-show-body montserrat d-flex flex-column justify-content-between w-100 position-relative">
        <div class="position-absolute" style="right: 0">
            <a href="{{ route('admin.directors.edit', $director->id) }}" class="btn btn-success"><i class="bi bi-pencil-square"></i></a>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div>
            <h3>{{ $director->name }}</h3>
            
            <!-- Sezione Biografia con Scrollbar -->
            <div class="actor-biography-scroll my-2">
                @if($director->biography)
                    <p class="small m-0">{{ $director->biography }}</p>
                @else
                    <p class="small m-0">No biography available.</p>
                @endif
            </div>

            <div class="mb-3">
                <span class="badge bg-secondary">Popularity: {{ number_format($director->popularity, 2) }}</span>
                @if($director->birthday)
                    <span class="badge bg-info text-dark">Birthday: {{ $director->birthday }}</span>
                @endif
                @if($director->deathday)
                    <span class="badge bg-dark">Deathday: {{ $director->deathday }}</span>
                @endif
            </div>

            @if($director->place_of_birth)
                <p class="mb-2 small"><strong>Place of Birth:</strong> {{ $director->place_of_birth }}</p>
            @endif

            {{-- Sezione film diretti dal regista (se presenti) --}}
            @if(isset($director->movies) && $director->movies->count() > 0)
                <h5 class="mt-3">Movies</h5>
                <div class="d-flex gap-3 mt-2 flex-wrap">
                    @foreach ($director->movies as $movie)
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
                <p class="d-inline m-0 text-muted">Director ID: {{ $director->id }}</p>
            </div>
            @if($director->birthday)
                <p class="d-inline m-0 text-muted">
                    Born: {{ $director->birthday }}
                </p>
            @endif
        </div>
    </div>
</div>