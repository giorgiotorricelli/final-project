@extends('admin.layouts.master')

@section('content')
    <div @style('margin: 30px; display: flex; justify-content: center; gap: 15px;')>
        <button id="listBtn" class="btn btn-warning" onclick="toggleView(true)"><i class=" bi bi-list-ol"></i></button>
        <button id="gridBtn" class="btn btn-outline-warning" onclick="toggleView(false)"><i class="bi bi-file-earmark-image"></i></button>
    </div>
    <div class="container" onclick="listSort(event)"> <!-- ascolta gli eventi di click per la sort -->
        <div id="gridView" class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 row-cols-xl-5 g-3" style="display: none;">
        @foreach ($movieList as $movie)
        <div class="col">
            <a href="{{ route('admin.movies.show', $movie->slug) }}" class="movie-link">
                <div class="movie-card-wrapper position-relative">
                    <div class="tag-box position-absolute ps-1 pt-1">
                        @foreach ($movie->genres as $genre)
                            <span class="tag" style="background-color: {{ $genre->color }}80; ">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                    <x-movie-card :movie="$movie"/>
                </div>
            </a>
        </div>
            
        @endforeach
        </div>
        <div id="listView">
            <table class="movies-table" style="width:100%">
                <thead>
                    <tr>
                    <th style="width:20%">Id  

                        <button id="sortByIdBtn" class="list-sort-btn bi bi-arrow-down-circle">
                        </button>
                    </th>
                    <th style="width:30%">Title  

                        <button id="sortByTitleBtn" class="list-sort-btn bi bi-circle">
                        </button>
                    </th>
                    <th style="width:20%">Release Date  

                        <button id="sortByDateBtn" class="list-sort-btn bi bi-circle">
                        </button>
                    </th>
                    <th style="width:20%">Rating  

                        <button id="sortByRateBtn" class="list-sort-btn bi bi-circle">
                        </button>
                    </th>
                    <th class="table-manage" style="width:10%">Manage</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <!-- questa è la vecchia logica blade -->
                    {{-- @foreach ($movieList as $movie)
                        <tr onclick="window.location='{{ route('admin.movies.show', $movie->slug) }}'">
                            <td>{{ $movie->id }}</td>
                            <td>{{ $movie->title }}</td>
                            <td>{{ $movie->release_date }}</td>
                            <td>{{ $movie->rating }}</td>
                            <td class="table-manage" onclick="event.stopPropagation()">
                                <div>
                                    <a href="{{ route('admin.movies.show', $movie->slug) }}" class="btn btn-success"><i class=" bi bi-pencil-square"></i></a>
                                    <!-- button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class=" bi bi-trash"></i>
                                    </button>
                                </div>
                                
                            </td>
                        </tr>
                    
                    @endforeach --}}
                </tbody>
            </table>
            
        </div>
        
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
                <p>You are about to delete this movie, are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- Inserisco l'action personalizzata tramite script -->
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
    
        let sortedList = @json($movieList); // prendo l'array associativo php e lo trasformo in oggetto js
        let tbody = document.getElementById('tbody'); // prendo il body della tabella
        let tempContainer = ''; // uso un container temporaneo

        function sortedBy(btn) { //qua passeremo l'event.target
            // abbiamo 4 tipi di sort: id, title, release_date, rating. Li capiamo dagli HTML id dei pulsanti
            // la funzione deve ordinare sortedList senza ritornare nulla :)

            if (btn.id === 'sortByIdBtn') { // sort per id
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => {
                        return b.id - a.id;
                    });
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => {
                        return a.id - b.id;
                    });
                }
            }

            if (btn.id === 'sortByTitleBtn') { // sort per title
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => {
                        return b.title.localeCompare(a.title); // uso local compare che organizza anche per caratteri speciali
                    });
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => {
                        return a.title.localeCompare(b.title);
                    });
                } 
            }

            if (btn.id === 'sortByDateBtn') { // sort per date
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => {
                        return new Date(b.release_date) - new Date(a.release_date); // uso date per conversione da str a date
                    });
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => {
                        return new Date(a.release_date) - new Date(b.release_date);
                    });
                } 
            }

            if (btn.id === 'sortByRateBtn') { // sort per rating
                if (btn.classList.contains('bi-arrow-up-circle')) {
                    sortedList.sort((a, b) => {
                        return b.rating - a.rating; 
                    });
                } else if (btn.classList.contains('bi-arrow-down-circle')) {
                    sortedList.sort((a, b) => {
                        return a.rating - b.rating;
                    });
                } 
            }

            renderMovies();

        }

        function setDestroyDestination(event) {
            const destination = event.target.getAttribute('data-movie-slug');
            // prendo il form dentro il modale
            const deletionForm = document.getElementById('modalDeletionForm');
            deletionForm.action = `/admin/movies/${destination}`;
            
        }

        function renderMovies() {
            
            tbody.innerHTML = ``; // svuoto tbody

            sortedList.forEach(movie => { // riempio il container
                tempContainer += `<tr onclick="window.location='http://localhost:8000/admin/movies/${movie.slug}'">
                            <td>${movie.id}</td>
                            <td>${movie.title}</td>
                            <td>${movie.release_date}</td>
                            <td>${movie.rating}</td>
                            <td class="table-manage" onclick="event.stopPropagation()">
                                <div>
                                    <!-- button trigger edit page -->
                                    <a href="http://localhost:8000/admin/movies/${movie.slug}/edit" class="btn btn-success"><i class=" bi bi-pencil-square"></i></a>
                                    <!-- button trigger deletion modal -->
                                    <button type="button" class="btn btn-primary bi bi-trash" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-movie-slug="${movie.slug}" onclick="setDestroyDestination(event)">
                                    </button>
                                </div>
                                
                            </td>
                        </tr>` ;
            });

            tbody.innerHTML = tempContainer; //trasporto tutto in tbody
            tempContainer = ''; // svuoto temp
        }

        renderMovies();

        function toggleView(isList) {
           document.getElementById('gridView').style.display = isList ? 'none' : 'flex';
           document.getElementById('listView').style.display = isList ? 'block' : 'none';
           document.getElementById('gridBtn').classList.toggle('btn-outline-warning');
           document.getElementById('gridBtn').classList.toggle('btn-warning');
           document.getElementById('listBtn').classList.toggle('btn-outline-warning');
           document.getElementById('listBtn').classList.toggle('btn-warning');
        }

        function listSort(event) {
            
            const target = event.target;
            const tagname = target.tagName;

            // controllo se il target è quello giusto
            if (!(tagname === 'BUTTON' && (target.classList.contains('bi-arrow-down-circle')
             || target.classList.contains('bi-arrow-up-circle') 
             || target.classList.contains('bi-circle')))) return;
            

            //se contiene le frecce quindi non è un new target
            if (target.classList.contains('bi-arrow-down-circle') || target.classList.contains('bi-arrow-up-circle')) {
                target.classList.toggle('bi-arrow-down-circle');
                target.classList.toggle('bi-arrow-up-circle');


                // *logica visualizzazione lista*

                sortedBy(target);
                

                return;

            } else {
                target.classList.remove('bi-circle'); // se non ne ha rimuove il cerchio per renderlo unico per il controllo successivo
            }

            // prendo tutti i pulsanti di sorting
            let allBtns = document.querySelectorAll('.list-sort-btn');
            
            allBtns.forEach(btn => {
                if (btn.classList.length > 2) { // quindi il bottone con le frecce
                    btn.classList.remove('bi-arrow-down-circle','bi-arrow-up-circle');
                    btn.classList.add('bi-circle');
                } 

            });
            
            event.target.classList.add('bi-arrow-down-circle'); // infine aggiungo freccia giù al target

            // *logica visualizzazione lista*
            sortedBy(target);

        }
    </script>
    
@endsection