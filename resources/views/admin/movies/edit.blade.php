@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <form action="{{ route('admin.movies.update', $movie->slug) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- Inserisco il metodo put per la edit -->
        <div class="mb-3">
            <label for="title" class="form-label">Tilte</label>
            <input class="form-control w-25"  type="text" name="title" id="title" required value="{{ old('title') !== null ? old('title') : $movie->title }}">
            <!-- Mostra l'errore specifico per il campo 'title' -->
            @error('title')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
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
            <div class=" d-flex align-items-center gap-3 h-100">
                <input onchange="ratingHandler()" class="form-control w-25"  type="range" min="1" max="10" step="0.01" name="rating" id="rating" value="{{ old('rating') !== null ? old('rating') : $movie->rating }}">
                <span id="rating-display" style="text-align: center; min-width: 50px; border: 2px solid white; border-radius: 10px">{{ old('rating') !== null ? old('rating') : $movie->rating }}</span>
            </div>
            
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label><br>
            <textarea name="description" id="description" class=" w-100 p-2" style="border-radius: 15px; min-height: 100px;">{{  old('description') !== null ? old('description') : $movie->description  }}</textarea>
            @error('description')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            
            
            <div class=" d-flex align-items-center gap-3" >
                <div class="w-50">
                    <label for="poster_path" class="form-label">Vertical Poster</label>
                    <div class=" d-flex gap-3">
                        <input class="form-control"  type="file" name="poster_path" id="poster_path" accept="image/*">
                        <button id="posterPathCanc" class=" btn btn-light" type="button" onclick="drawback(event)">Cancel</button>
                    </div>
                    
                </div>
                <div class="edit-vertical-poster-container">
                    <img src="{{ Storage::url($movie->poster_path) }}" alt="vertical-poster" class="edit-vertical-poster-img">
                </div>
                <div class="align-self-end">
                        <label for="hasPosterCheck" class="form-label mb-0">No Vertical Poster</label>
                        <input class="d-inline" type="checkbox" name="hasPosterCheck" id="hasPosterCheck">
                    </div>
            </div>
            @error('poster_path')
                <span style="color: red; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <div class=" d-flex align-items-center gap-3" >
                <div class="w-50">
                    <label for="backdrop_path" class="form-label">Horizontal Poster</label>
                    <div class=" d-flex gap-3">
                        <input class="form-control"  type="file" name="backdrop_path" id="backdrop_path" accept="image/*">
                        <button id="backdropPathCanc" class=" btn btn-light" type="button" onclick="drawback(event)">Cancel</button>
                    </div>
                    
                </div>
                <div class="edit-horizontal-poster-container">
                    <img src="{{ Storage::url($movie->backdrop_path) }}" alt="horizontal-poster" class="edit-horizontal-poster-img">
                </div>
                <div class=" align-self-end">
                    <label for="hasBackdropCheck" class="form-label mb-0">No Horizontal Poster</label>
                    <input class="d-inline" type="checkbox" name="hasBackdropCheck" id="hasBackdropCheck">
                </div>
            </div>
            
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

        const moviePoster = @json(Storage::url($movie->poster_path ?? ''));
        const movieBackdrop = @json(Storage::url($movie->backdrop_path ?? ''));
        const hasPoster = @json(!empty($movie->poster_path));
        const hasBackdrop = @json(!empty($movie->backdrop_path));


        // metto degli eventlistener change agli input di tipo file per vedere quando l'utente seleziona un file
        let inputVertical = document.getElementById('poster_path');
        let imgVertical = document.querySelector('.edit-vertical-poster-img');
        let inputHorizontal = document.getElementById('backdrop_path');
        let imgHorizontal = document.querySelector('.edit-horizontal-poster-img');

        inputVertical.addEventListener('change', (event) => {
            // Prendiamo il file selezionato dall'utente
            const file = event.target.files[0]; // prendiamo il primo perchè di default restituisce una lista

            if (file) {
                // Creiamo un URL temporaneo locale per il file
                imgVertical.src = URL.createObjectURL(file);
            }


        });

        inputHorizontal.addEventListener('change', (event) => {
            // Prendiamo il file selezionato dall'utente
            const file = event.target.files[0]; // prendiamo il primo perchè di default restituisce una lista

            if (file) {
                // Creiamo un URL temporaneo locale per il file
                imgHorizontal.src = URL.createObjectURL(file);
            }


        });

        function drawback(event) {
            console.log('hi');
            
            const isPosterBtn = event.target.id === 'posterPathCanc';
            const input = isPosterBtn ? inputVertical : inputHorizontal;
            const img = isPosterBtn ? imgVertical : imgHorizontal;
            const originalImage = isPosterBtn ? moviePoster : movieBackdrop;
            const hasOriginal = isPosterBtn ? hasPoster : hasBackdrop;

            // Se l'input è già vuoto o se l'utente ha cliccato per rimettere l'immagine originale
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

            // Se l'utente aveva selezionato un file e vuole annullare la scelta corrente
            input.value = ''; // Svuota l'input file
            
            // Ripristina l'immagine originale del database (se presente) oppure svuota
            if (hasOriginal) {
                img.src = originalImage;
            } else {
                img.src = '';
                img.alt = '';
            }
        }
        
    </script>
    
@endsection