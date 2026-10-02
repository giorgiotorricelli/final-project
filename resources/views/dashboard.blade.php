@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fs-4 text-secondary my-4">
        {{ __('Dashboard') }}
    </h2>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ __('User Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
            <div class="d-flex justify-content-center mt-3">
                <a class=" d-inline" href="{{ route('admin.index' )}}"><button class=" btn btn-primary">Sezione admin</button></a>
            </div>
        </div>
    </div>
</div>
@endsection
