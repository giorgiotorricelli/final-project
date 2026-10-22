@extends('admin.layouts.master')

@section('content')
    <div @style('margin: 30px; display: flex; justify-content: center; gap: 15px;')>
        <button id="listBtn" class="btn btn-outline-warning" onclick="toggleView(true)"><i class=" bi bi-list-ol"></i></button>
        <button id="gridBtn" class="btn btn-warning" onclick="toggleView(false)"><i class="bi bi-file-earmark-image"></i></button>
    </div>
    <div class="container">
        <div id="gridView" class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 row-cols-xl-5 g-3">
        @foreach ($movieList as $movie)
        <div class="col">
            <a href="{{ route('admin.movies.show', $movie->slug) }}" class="movie-link">
                <div class="movie-card-wrapper">
                    <x-movie-card :movie="$movie"/>
                </div>
            </a>
        </div>
            
        @endforeach
        </div>
        <div id="listView" style="display: none;">
            <table class="movies-table" style="width:100%">
                <tr>
                    <th style="width:20%">Id</th>
                    <th style="width:30%">Title</th>
                    <th style="width:20%">Release Date</th>
                    <th style="width:20%">Rating</th>
                    <th class="table-manage" style="width:10%">Manage</th>
                </tr>
                @foreach ($movieList as $movie)
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
                
                @endforeach
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
            <form action="{{ route('admin.movies.destroy', $movie) }}" method="post">
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
        function toggleView(isList) {
           document.getElementById('gridView').style.display = isList ? 'none' : 'flex';
           document.getElementById('listView').style.display = isList ? 'block' : 'none';
           document.getElementById('gridBtn').classList.toggle('btn-outline-warning');
           document.getElementById('gridBtn').classList.toggle('btn-warning');
           document.getElementById('listBtn').classList.toggle('btn-outline-warning');
           document.getElementById('listBtn').classList.toggle('btn-warning');
        }
    </script>
    
@endsection