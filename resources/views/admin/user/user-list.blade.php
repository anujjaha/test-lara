@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">User List</div>
                <div class="card-body">
                    <table class="table"> 
                        <tr>
                            <th> Name </th>
                            <th> Email </th>
                            <th> Contact Number </th>
                            <th> Access Groups </th>
                            <th> Action </th>
                        </tr>

                        @if(isset($users) && count($users))
                            @foreach($users as $user)
                                <tr>
                                    <td> {{ $user->name }} </td>
                                    <td> {{ $user->email }} </td>
                                    <td> {{ $user->contact_number }} </td>
                                    <td> 
                                        @if(isset($user->user_groups) && count($user->user_groups))
                                            @foreach($user->user_groups as $usergroup)
                                                <a href="{!! route('admin.groups.show', ['id' => $usergroup->group->id]) !!}">
                                                    <span> {{ ucfirst($usergroup->group->name) }} </span>
                                                </a>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td> 
                                        <a class="btn btn-xs btn-success" href="{{ route('admin.edit-user', ['id' => $user->id]) }}">
                                            Edit
                                        </a>
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.delete-user', ['id' => $user->id]) }}">
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
