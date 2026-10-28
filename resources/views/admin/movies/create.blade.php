@extends('admin.layouts.master')

@use('App\Models\Genre')
@php
    $genres = Genre::all();
@endphp

@section('content')
    <div class="container">
        <form action="{{ route('admin.movies.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <label for="title" class="form-label">Tilte</label>
                <input class="form-control"  type="text" name="title" id="title" required value="{{ old('title') }}">
                @error('title')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div style="translate: 0 50%;">
                <div class="dropstart">
                    <!-- Aggiunto data-bs-auto-close="outside" per dire a bootstrap di chiudere il dropdown solo cliccando all'esterno-->
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        Choose genres
                    </button>
                    @error('genre_error')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                    
                    <div class="dropdown-menu p-3" style="min-width: 250px;">
                        @foreach ($genres as $genre)
                            <div class="form-check form-switch mb-1">
                                <input name="genre_ids[]" id="genre-{{ $genre->id }}" value="{{ $genre->id }}" class="form-check-input" type="checkbox" role="switch">
                                <label class="form-check-label" for="genre-{{ $genre->id }}">{{ $genre->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Mostra l'errore specifico per il campo 'title' -->
            
        </div>
        <div class="mb-3">
            <label for="release_date" class="form-label">Release Date</label>
            <input class="form-release-date form-control w-25" type="date" name="release_date" id="release_date" value="{{ old('release_date') }}" required>
            @error('release_date')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <div class=" d-flex align-items-center gap-3 h-100">
                <input onchange="ratingHandler()" class="form-control w-25"  type="range" min="1" max="10" step="0.01" name="rating" id="rating" value="{{ old('rating') }}">
                <span id="rating-display" style="text-align: center; min-width: 50px; border: 2px solid white; border-radius: 10px">5.0</span>
            </div>
            
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label><br>
            <textarea name="description" id="description" class=" w-100 p-2" style="border-radius: 15px; min-height: 100px;">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="poster_path" class="form-label">Vertical Poster</label>
            <input class="form-control"  type="file" name="poster_path" id="poster_path">
            @error('poster_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="backdrop_path" class="form-label">Horizontal Poster</label>
            <input class="form-control"  type="file" name="backdrop_path" id="backdrop_path">
            @error('backdrop_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
            @error('slug')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <button class=" btn btn-warning" type="submit">Confirm</button>
        

    </form>
    </div>
    <script>
        function ratingHandler() {
            let value = document.getElementById('rating').value;
            document.getElementById('rating-display').innerText = value;
            
        }
        
    </script>
    
@endsection