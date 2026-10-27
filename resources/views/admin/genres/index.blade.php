@extends('admin.layouts.master')

@use('Illuminate\Support\Str')

@section('content')
<div class="container position-relative">
    <div class="dropdown position-absolute" style="right: 0;">
        <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-plus-square"></i>
        </button>
        <div class="dropdown-menu create-genre-dropdown-menu" style="min-width: 300px">
            <form action="{{ route('admin.genres.store') }}" method="post">
                @csrf
                <div class="mx-1">
                    <label for="name" class="form-label" style="color: white">Name</label>
                    <input class="form-control"  type="text" name="name" id="name" required value="{{ old('name') }}" onclick="removeCreateErrorVisibility()">
                    <!-- Mostra l'errore specifico per il campo 'name' -->
                    @error('name')
                        <span id="create-error-span" class="d-block" style="color: red; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                    <button class="btn btn-outline-primary btn-sm mt-2" type="submit">Confirm</button>
                </div>
                
            </form>
        </div>
    </div>
    <ul style="list-style: none;" class="m-0 p-0 mt-3">
        @foreach ($genres as $genre)
            <li class="w-50 d-flex align-items-center justify-content-between">
                <!-- form per l'update -->
                <form action="{{ route('admin.genres.update', $genre->id) }}" method="post" 
                    class="d-none w-50" id="form{{ Str::camel($genre->name) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="d-flex g-2 position-relative">
                        <input type="text" name="name" id="{{ Str::camel($genre->name) }}" 
                            value="{{ old('name_' . (string) $genre->id) !== null ? old('name_' . (string) $genre->id) : $genre->name }}"
                             class=" px-1" onclick="handleErrorVisibility()">
                        <button type="submit" class="genres-btn-edit btn btn-sm bi bi-hand-thumbs-up"></button>
    <!-- Quà ho personalizzato  il campo del nome dell'errore per distinzione, altrimenti una volta generato un errore
     quando la pagina torna indietro lo trasmette a tutti i form, stessa cosa per gli input -->
                        @error('name_' . (string) $genre->id)
                        <span class="position-absolute error-edit-genre-span">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
                <h5 class="m-0" id="title{{ Str::camel($genre->name) }}">{{ $genre->name }}</h5>
                <div> <!-- Metto la stessa stringa a id e name per passarla tramite event e selezionare l'input -->
                    <button type="button" class="genres-btn-edit btn btn-sm bi bi-pencil" 
                    name="title{{ Str::camel($genre->name) }}" onclick="handleUpdateVisibility(event)"></button>

                    <!-- Delete :) -->
                    <button type="button" class="genres-btn-delete btn btn-sm bi bi-trash"></button>
                </div>
            </li>
                
        @endforeach
    </ul>
</div>
<script>
    const createErrorSpan = document.getElementById('create-error-span');
    
    // semplice funzione per rimuovere la visibility dell'errore 
    function removeCreateErrorVisibility() {
        if (createErrorSpan) {
            createErrorSpan.classList.add('d-none');
        }
        
    }

    function handleUpdateVisibility(event) {
        const btn = event.target; 
        const btnName = btn.name; // sarà una stringa di valore uguale a id di input
        const titleInput = document.getElementById(btnName);

        const formSelector = btnName.replace("title", "form"); //mi serve per prendere l'id del form
        const form = document.getElementById(formSelector);

        
        titleInput.classList.toggle('d-none');
        btn.classList.toggle('bi-pencil');
        btn.classList.toggle('bi-x');
        form.classList.toggle('d-none');
        form.classList.toggle('d-block');
        
    }

    function handleErrorVisibility() {
        document.querySelector('.error-edit-genre-span').classList.add('d-none');
    }
</script>
    
@endsection