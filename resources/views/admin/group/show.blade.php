@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Group List</div>
                <div class="card-body">
                    <h4>Name :  {{ $group->name }}</h4>
                    <div class="col-md-12">
                        Attached Roles:
                        @if(isset($group->group_roles))
                            @foreach($group->group_roles as $groupRrole)
                                <p> {{ ucfirst($groupRrole->role->name) }} </p>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-md-12">
                        <a href="{!! route('admin.groups.create') !!}" class="pull-right btn btn-success">
                            Add
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
