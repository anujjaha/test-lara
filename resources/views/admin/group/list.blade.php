@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Group List</div>
                <div class="card-body">
                    <div class="col-md-12">
                        <a href="{!! route('admin.groups.create') !!}" class="pull-right btn btn-success">
                            Add
                        </a>
                    </div>
                    <table class="table"> 
                        <tr>
                            <th> Name </th>
                            <th> Roles </th>
                            <th> Action </th>
                        </tr>

                        @if(isset($groups) && count($groups))
                            @foreach($groups as $group)
                                <tr>
                                    <td> {{ ucfirst($group->name) }} </td>
                                    <td>
                                        @if(isset($group->group_roles) && count($group->group_roles))
                                            @foreach($group->group_roles as $groupRole)
                                                <a href="{{ route('admin.roles.show', ['id' => $groupRole->role_id ]) }}">
                                                    {{ ucfirst($groupRole->role->name) }}
                                                </a>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-xs btn-success" href="{{ route('admin.groups.edit', ['id' => $group->id]) }}">
                                            Edit
                                        </a> 
                                        @if(!$loop->first)
                                            <a class="btn btn-xs btn-primary" href="{{ route('admin.groups.destroy', ['id' => $group->id]) }}">
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
