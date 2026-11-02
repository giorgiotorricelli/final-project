@extends('admin.layouts.master')
@use('Illuminate\Support\Facades\Storage')

@section('content')
    
    <div class="backdrop-wrapper" style="--bg-image: url('{{ Storage::url('neon-background.png') }}')">
        <div class="show-container d-flex align-items-center h-100">
            <x-actor-card-show :actor="$actor"/>
        </div>
    </div>

    <!-- Modal di eliminazione -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteModalLabel">Deleting</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to delete this actor, are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="{{ route('admin.actors.destroy', $actor->id) }}" method="post">
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