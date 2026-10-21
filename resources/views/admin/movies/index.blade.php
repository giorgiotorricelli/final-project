@extends('admin.layouts.master')

@section('content')
    <div @style('margin: 30px; display: flex; justify-content: center; gap: 15px;')>
        <button class="btn btn-warning" onclick="toggleView(true)"><i class=" bi bi-list-ol"></i></button>
        <button class="btn btn-warning" onclick="toggleView(false)"><i class="bi bi-file-earmark-image"></i></button>
    </div>
    <div class="container">
        <div id="gridView" class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 row-cols-xl-5 g-3">
        @foreach ($movieList as $movie)
        <div class="col">
            <a href="{{ route('admin.movies.show', $movie->slug) }}">
                <div class="movie-card-wrapper">
                    <x-movie-card :movie="$movie"/>
                </div>
            </a>
        </div>
            
        @endforeach
        </div>
        <div id="listView" style="display: none;">
            <table style="width:100%">
                <tr>
                    <th style="width:20%">Id</th>
                    <th style="width:30%">Title</th>
                    <th style="width:20%">Release Date</th>
                    <th style="width:20%">Rating</th>
                    <th class="table-manage" style="width:10%">Manage</th>
                </tr>
                @foreach ($movieList as $movie)
                    <tr>
                        <td>{{ $movie->id }}</td>
                        <td>{{ $movie->title }}</td>
                        <td>{{ $movie->release_date }}</td>
                        <td>{{ $movie->rating }}</td>
                        <td class="table-manage">
                            <div>
                                <a href="" class="btn btn-success"><i class=" bi bi-pencil-square"></i></a>
                                <a href="" class="btn btn-info"><i class=" bi bi-trash"></i></a>
                            </div>
                            
                        </td>
                    </tr>
                
                @endforeach
            </table>
            
        </div>
        
    </div>
    <script>
        function toggleView(isList) {
            document.getElementById('listView').style.display = isList ? 'block' : 'none';
            document.getElementById('gridView').style.display = isList ? 'none' : 'flex';
        }
    </script>
    
@endsection