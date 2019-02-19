@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4 class="card-title">{{ $user->name }}</h4>
                    <p class="card-text">Email : {{ $user->email }}</p>
                    <p class="card-text">Contact Number : {{ $user->contact_number }}</p>
                    @if(!isAdmin())
                        <a href="{{ route('delete-account') }}" class="card-link btn btn-danger">Delete Account</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
