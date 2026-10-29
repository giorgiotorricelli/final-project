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
                    
                    <div class="d-flex g-2 position-relative align-items-center">
                        <input type="text" name="name" id="{{ Str::camel($genre->name) }}" 
                            value="{{ old('name_' . (string) $genre->id) !== null ? old('name_' . (string) $genre->id) : $genre->name }}"
                             class=" px-1" onclick="handleErrorVisibility()" @style('border-radius: 10px; border-style: none;')>
                        <input type="color" name="color" id="color{{ Str::camel($genre->name) }}" value="{{ $genre->color }}"
                        style="width: 15px; height: 15px;">
                        <button type="submit" class="genres-btn-edit btn btn-sm bi bi-hand-thumbs-up"></button>
    <!-- Quà ho personalizzato  il campo del nome dell'errore per distinzione, altrimenti una volta generato un errore
     quando la pagina torna indietro lo trasmette a tutti i form, stessa cosa per gli input -->
                        @error('name_' . (string) $genre->id)
                        <span class="position-absolute error-edit-genre-span">{{ $message }}</span>
                        @enderror
                        @error('color_' . (string) $genre->id)
                        <span class="position-absolute error-edit-genre-span">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
                <h5 style="background-color: {{ $genre->color }};
                padding: 2px 7px; border-radius: 10px;" class=" m-0" id="title{{ Str::camel($genre->name) }}">{{ $genre->name }}</h5>
                <div> <!-- Metto la stessa stringa a id e name per passarla tramite event e selezionare l'input -->
                    <button type="button" class="genres-btn-edit btn btn-sm bi bi-pencil" 
                    name="title{{ Str::camel($genre->name) }}" onclick="handleUpdateVisibility(event)"></button>

                    <!-- Delete :) --><!-- button trigger deletion modal -->
                    <button type="button" class="genres-btn-delete btn btn-sm bi bi-trash" 
                    data-bs-toggle="modal" data-bs-target="#deleteModal" data-genre-id="{{ $genre->id }}"
                    onclick="setDestroyDestination(event)"></button>
                    
                </div>
            </li>
                
        @endforeach
    </ul>
</div>
<!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">Deleting</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are about to delete this genre, are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- Inserisco l'action tramite script -->
                <form id="modalDeletionForm" action="" method="post">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-info" type="submit">
                        Confirm
                    </button>
                </form>
            </div>
            </div>
        </div>
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

    function setDestroyDestination(event) {
        const destination = event.target.getAttribute('data-genre-id');
        // prendo il form dentro il modale
        const deletionForm = document.getElementById('modalDeletionForm');
        deletionForm.action = `/admin/genres/${destination}`;
        
    }
</script>
    
@endsection