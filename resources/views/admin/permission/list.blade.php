@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Permission List</div>
                <div class="card-body">
                    <div class="col-md-12">
                        <a href="{!! route('admin.permissions.create') !!}" class="pull-right btn btn-success">
                            Add
                        </a>
                    </div>
                    <table class="table"> 
                        <tr>
                            <th> Name </th>
                            <th> Attached Roles </th>
                            <th> Action </th>
                        </tr>

                        @if(isset($permissions) && count($permissions))
                            @foreach($permissions as $permission)
                                <tr>
                                    <td> {{ ucfirst($permission->name) }} </td>
                                    <td>
                                        @if(isset($permission->role_permissions) && count($permission->role_permissions))
                                            @foreach($permission->role_permissions as $rolePermission)
                                                <a href="{{ route('admin.roles.show', ['id' => $rolePermission->role->id]) }}">
                                                    <span class="label">{{ ucfirst($rolePermission->role->name) }}</span>
                                                </a>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-success" href="{{ route('admin.permissions.edit', ['id' => $permission->id]) }}">
                                            Edit
                                        </a> 
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.permissions.destroy', ['id' => $permission->id]) }}">
                                                Delete
                                        </a>
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
