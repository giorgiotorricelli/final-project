@extends('admin.layouts.master')

@use('App\Models\Genre')
@use('App\Models\Director')
@use('App\Models\Actor')
@php
    $genres = Genre::all()->sortBy('name');
    $directors = Director::all()->sortBy('name');
    $actors = Actor::all()->sortBy('name');
@endphp

@section('content')
    <div class="container">
        <form action="{{ route('admin.movies.update', $movie) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <label for="title" class="form-label">Title</label>
                <input class="form-control" type="text" name="title" id="title" required value="{{ old('title') !== null ? old('title') : $movie->title }}">
                @error('title')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="translate: 0 50%;" class="d-flex gap-2">
                <!-- Dropdown Generi -->
                <div class="dropstart">
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        Choose genres
                    </button>
                    @error('genre_error')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                    
                    <div class="dropdown-menu p-3" style="min-width: 250px;">
                        @foreach ($genres as $genre)
                            <div class="form-check form-switch mb-1">
                                <input name="genre_ids[]" id="genre-{{ $genre->id }}" value="{{ $genre->id }}" class="form-check-input" type="checkbox" role="switch"
                                @if ($movie->genres->contains($genre->id))
                                    @checked(true)
                                @endif>
                                <label class="form-check-label" for="genre-{{ $genre->id }}">{{ $genre->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dropdown Attori -->
                <div class="dropstart">
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        Choose actors
                    </button>
                    @error('actor_ids')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                    
                    <div class="dropdown-menu p-3" style="min-width: 350px; max-height: 200px; overflow-y: auto;">
                        @foreach ($actors as $actor)
                            <div class="form-check form-switch mb-1">
                                <input name="actor_ids[]" id="actor-{{ $actor->id }}" value="{{ $actor->id }}" class="form-check-input" type="checkbox" role="switch"
                                @if ($movie->actors->contains($actor->id))
                                    @checked(true)
                                @endif>
                                <label class="form-check-label" for="actor-{{ $actor->id }}">{{ $actor->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dropdown Regista (Singolo) -->
                <div class="position-relative">
                    <div class="dropstart">
                        <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            Choose Director
                        </button>
                        
                        <div class="dropdown-menu p-3" style="min-width: 350px; max-height: 200px; overflow-y: auto;">
                            @foreach ($directors as $director)
                                <div class="form-check form-switch mb-1">
                                    <input name="director_id" id="director-{{ $director->id }}" value="{{ $director->id }}" class="form-check-input" type="radio" role="switch"
                                    @checked(old('director_id', $movie->director_id) == $director->id)>
                                    <label class="form-check-label" for="director-{{ $director->id }}">{{ $director->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('director_id')
                        <span class="position-absolute" style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="release_date" class="form-label">Release Date</label>
            <input class="form-release-date form-control w-25" type="date" name="release_date" id="release_date" value="{{ old('release_date') !== null ? old('release_date') : $movie->release_date }}" required>
            @error('release_date')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <div class="d-flex align-items-center gap-3 h-100">
                <input onchange="ratingHandler()" class="form-control w-25" type="range" min="1" max="10" step="0.01" name="rating" id="rating" value="{{ old('rating') !== null ? old('rating') : $movie->rating }}">
                <span id="rating-display" style="text-align: center; min-width: 50px; border: 2px solid white; border-radius: 10px">{{ old('rating') !== null ? old('rating') : $movie->rating }}</span>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label><br>
            <textarea name="description" id="description" class="w-100 p-2" style="border-radius: 15px; min-height: 100px;">{{ old('description') !== null ? old('description') : $movie->description }}</textarea>
            @error('description')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-50">
                    <label for="poster_path" class="form-label">Vertical Poster</label>
                    <div class="d-flex gap-3">
                        <input class="form-control" type="file" name="poster_path" id="poster_path" accept="image/*">
                        <button id="posterPathCanc" class="btn btn-light" type="button" onclick="drawback(event)">Cancel</button>
                    </div>
                </div>
                <div class="edit-vertical-poster-container">
                    <img src="{{ Storage::url($movie->poster_path) }}" alt="vertical-poster" class="edit-vertical-poster-img">
                </div>
                <div class="align-self-end">
                    <label for="noPosterCheck" class="form-label mb-0">No Vertical Poster</label>
                    <input class="d-inline" type="checkbox" name="noPosterCheck" id="noPosterCheck">
                </div>
            </div>
            @error('poster_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="w-50">
                    <label for="backdrop_path" class="form-label">Horizontal Poster</label>
                    <div class="d-flex gap-3">
                        <input class="form-control" type="file" name="backdrop_path" id="backdrop_path" accept="image/*">
                        <button id="backdropPathCanc" class="btn btn-light" type="button" onclick="drawback(event)">Cancel</button>
                    </div>
                </div>
                <div class="edit-horizontal-poster-container">
                    <img src="{{ Storage::url($movie->backdrop_path) }}" alt="horizontal-poster" class="edit-horizontal-poster-img">
                </div>
                <div class="align-self-end">
                    <label for="noBackdropCheck" class="form-label mb-0">No Horizontal Poster</label>
                    <input class="d-inline" type="checkbox" name="noBackdropCheck" id="noBackdropCheck">
                </div>
            </div>
            
            @error('backdrop_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
            @error('slug')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <button class="btn btn-warning" type="submit">Confirm</button>
    </form>
    </div>
    <script>
        function ratingHandler() {
            let value = document.getElementById('rating').value;
            document.getElementById('rating-display').innerText = value;
        }

        const moviePoster = @json(Storage::url($movie->poster_path ?? ''));
        const movieBackdrop = @json(Storage::url($movie->backdrop_path ?? ''));
        const hasPoster = @json(!empty($movie->poster_path));
        const hasBackdrop = @json(!empty($movie->backdrop_path));

        let inputVertical = document.getElementById('poster_path');
        let imgVertical = document.querySelector('.edit-vertical-poster-img');
        let inputHorizontal = document.getElementById('backdrop_path');
        let imgHorizontal = document.querySelector('.edit-horizontal-poster-img');

        inputVertical.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                imgVertical.src = URL.createObjectURL(file);
            }
        });

        inputHorizontal.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                imgHorizontal.src = URL.createObjectURL(file);
            }
        });

        function drawback(event) {
            const isPosterBtn = event.target.id === 'posterPathCanc';
            const input = isPosterBtn ? inputVertical : inputHorizontal;
            const img = isPosterBtn ? imgVertical : imgHorizontal;
            const originalImage = isPosterBtn ? moviePoster : movieBackdrop;
            const hasOriginal = isPosterBtn ? hasPoster : hasBackdrop;

            if (input.value === '') {
                if (hasOriginal) {
                    img.src = originalImage;
                    img.alt = isPosterBtn ? 'vertical-poster' : 'horizontal-poster';
                } else {
                    img.src = '';
                    img.alt = '';
                }
                return;
            }

            input.value = ''; 
            
            if (hasOriginal) {
                img.src = originalImage;
            } else {
                img.src = '';
                img.alt = '';
            }
        }
    </script>
@endsection