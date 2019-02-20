@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Role List</div>
                <div class="card-body">
                    <div class="col-md-12">
                        <a href="{!! route('admin.roles.create') !!}" class="pull-right btn btn-success">
                            Add
                        </a>
                    </div>
                    <table class="table"> 
                        <tr>
                            <th> Name </th>
                            <th> Permissions </th>
                            <th> Action </th>
                        </tr>

                        @if(isset($roles) && count($roles))
                            @foreach($roles as $role)
                                <tr>
                                    <td> {{ ucfirst($role->name) }} </td>
                                    <td>
                                        @if(isset($role->role_permissions) && count($role->role_permissions))
                                            @foreach($role->role_permissions as $rolePermission)
                                                <span class="label">{{ ucfirst($rolePermission->permission->name) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-success" href="{{ route('admin.roles.edit', ['id' => $role->id]) }}">
                                            Edit
                                        </a> 
                                        @if(!$loop->first)
                                            <a class="btn btn-xs btn-primary" href="{{ route('admin.roles.destroy', ['id' => $role->id]) }}">
                                                Delete
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
