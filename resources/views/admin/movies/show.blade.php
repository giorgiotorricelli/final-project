@extends('admin.layouts.master')
@use('Illuminate\Support\Facades\Storage')

@section('content')
    
    <div class="backdrop-wrapper" style="--bg-image: url('{{ $movie->backdrop_path ? Storage::url($movie->backdrop_path) : 'https://placehold.co/1280x400/481c7a/000/png?text=:)' }}')">
        <div class="show-container d-flex align-items-center h-100">
            <x-movie-card-show :movie="$movie"/>
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
@endsection