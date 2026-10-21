@extends('admin.layouts.master')

@section('content')
    <form action="{{ route('admin.movies.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title">Tilte</label>
            <input type="text" name="title" id="title" required value="{{ old('title') }}">
            <!-- Mostra l'errore specifico per il campo 'title' -->
            @error('title')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="release_date">Release Date</label>
            <input type="date" name="release_date" id="release_date" value="{{ old('release_date') }}" required>
            @error('release_date')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rating">Rating</label>
            <input type="range" min="1" max="10" step="0.25" name="rating" id="rating" value="{{ old('rating') }}">
        </div>
        <div class="mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="poster_path">Vertical Poster</label>
            <input type="file" name="poster_path" id="poster_path">
            @error('poster_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="backdrop_path">Horizontal Poster</label>
            <input type="file" name="backdrop_path" id="backdrop_path">
            @error('backdrop_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
            @error('slug')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Confirm</button>
        

    </form>
@endsection