@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Role List</div>
                <div class="card-body">
                    <h4>Name :  {{ $role->name }}</h4>
                    <div class="col-md-12">
                        Attached Permissions:
                        @if(isset($role->role_permissions))
                            @foreach($role->role_permissions as $rolePermission)
                                <p>
                                    {{ ucfirst($rolePermission->permission->name) }}
                                </p>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-md-12">
                        <a href="{!! route('admin.roles.create') !!}" class="pull-right btn btn-success">
                            Add
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
