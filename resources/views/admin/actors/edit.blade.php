@extends('admin.layouts.master')

@use('App\Models\Movie')
@use('Illuminate\Support\Facades\Storage')

@php
    if (count($actor->movies) > 0) {
        $ids = $actor->movies->pluck('id'); //estraggo gli id dai film dell'attore

        $moviesChecked = Movie::whereIn('id', $ids)->get();
        $moviesUnchecked = Movie::whereNotIn('id', $ids)->get();
    } else {
        $moviesUnchecked = Movie::all();
    }

@endphp

@section('content')
    <div class="container">
        <form action="{{ route('admin.actors.update', $actor) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3 d-flex justify-content-between">
            <div>
                <label for="name" class="form-label">Name</label>
                <input class="form-control"  type="text" name="name" id="name" required value="{{ old('name') ?? $actor->name }}">
                @error('name')
                    <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            <div style="translate: 0 50%;" class=" d-flex gap-2">
                <div class="dropstart" class=" position-relative">
                    <!-- Aggiunto data-bs-auto-close="outside" per dire a bootstrap di chiudere il dropdown solo cliccando all'esterno -->
                    <button class="btn btn-outline-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        Choose Movies
                    </button>
                    
                    <!-- Aggiunto max-height e overflow-y: auto per consentire scroll-->
                    <div class="dropdown-menu p-3" style="min-width: 400px; max-height: 200px; overflow-y: auto;">
                        @if (isset($moviesChecked))
                            @foreach ($moviesChecked as $movie)
                            <div class="form-check form-switch mb-1">
                                <input name="movie_slugs[]" id="movie-{{ $movie->slug }}" value="{{ $movie->slug }}" class="form-check-input" 
                                type="checkbox" role="switch" @checked(true)>
                                <label class="form-check-label" for="movie-{{ $movie->slug }}">{{ $movie->title }}</label>
                            </div>
                            @endforeach
                        @endif
                        @foreach ($moviesUnchecked as $movie)
                            <div class="form-check form-switch mb-1">
                                <input name="movie_slugs[]" id="movie-{{ $movie->slug }}" value="{{ $movie->slug }}" 
                                class="form-check-input" type="checkbox" role="switch">
                                <label class="form-check-label" for="movie-{{ $movie->slug }}">{{ $movie->title }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        @error('movie_slugs')
                        <span class=" position-absolute" style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                    </div>
                    
                </div>
            </div>
            
            <!-- Mostra l'errore specifico per il campo 'title' -->
            
        </div>
        <div class="mb-3">
            <label for="birthday" class="form-label">Birthday</label>
            <input class="form-release-date form-control w-25" type="date" name="birthday" id="birthday" 
            value="{{ old('birthday') ?? $actor->birthday }}" required> 
            @error('birthday')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="deathday" class="form-label">Deathday</label>
            <input class="form-release-date form-control w-25" type="date" name="deathday"
             id="deathday" value="{{ old('deathday') ?? $actor->deathday }}">
            @error('deathday')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="biography" class="form-label">Biography</label><br>
            <textarea name="biography" id="biography" class=" w-100 p-2" 
            style="border-radius: 15px; min-height: 100px;">{{ old('biography') ?? $actor->biography }}</textarea>
            @error('biography')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 w-50">
            <label for="place_of_birth" class="form-label">Place of birth</label>
            <input class="form-control"  type="text" name="place_of_birth" id="place_of_birth" 
            required value="{{ old('place_of_birth') ?? $actor->place_of_birth }}" maxlength="100">
            @error('place_of_birth')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class=" d-flex align-items-center gap-3" >
                <div class="w-50">
                    <label for="inputProfile" class="form-label">Profile Poster</label>
                    <input class="form-control"  type="file" name="profile_path" id="inputProfile" accept="image/*">
                    @error('profile_path')
                        <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                <button type="button" onclick="handleImgVisibility()" 
                class=" align-self-end btn btn-outline-warning d-none" id="cancelBtn">Cancel</button>
                <div class="create-actor-profile-img-container">
                    <img src="{{ Storage::url(old('profile_path') ?? $actor->profile_path) }}" alt="horizontal-poster" class="create-actor-profile-img" id="imgProfile">
                </div>
                <div class=" align-self-end">
                    <label for="noProfileCheck" class="form-label mb-0">No Profile Poster</label>
                    <input class="d-inline" type="checkbox" name="noProfileCheck" id="noProfileCheck">
                </div>
            </div>
        </div>
        <button class=" btn btn-warning" type="submit">Confirm</button>
        

    </form>
    </div>
    <script>
        /*
        I casi sono tre:
            - Non c'è nessuna immagine
            - C'è un immagine
            - C'è old('value')
        
        */
        
        const inputProfile = document.getElementById('inputProfile');
        const imgProfile = document.getElementById('imgProfile');
        const cancelBtn = document.getElementById('cancelBtn');
        const imgContainer = document.querySelector('.create-actor-profile-img-container');


        inputProfile.addEventListener('change', (event) => {
            // Prendiamo il file selezionato dall'utente
            file = event.target.files[0]; // prendiamo il primo perchè di default restituisce una lista

            if (file) {
                // Creiamo un URL temporaneo locale per il file
                imgProfile.src = URL.createObjectURL(file);
            }
            
            imgContainer.classList.remove('d-none');
            cancelBtn.classList.remove('d-none');

        });

        function handleImgVisibility() {
            imgContainer.classList.add('d-none');

            if (inputProfile.files[0]) { // se c'è un file
                inputProfile.value = ''; // lo svuoto
                imgProfile.src = '';
                cancelBtn.classList.add('d-none');
            } 

        } 
    </script>
    
@endsection