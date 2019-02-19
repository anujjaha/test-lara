@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Create Group</div>
                
                <div class="card-body">
                    {!! Form::open(['route' => ['admin.groups.store'], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post', 'id' => 'admin-create-group', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data']) !!}
                    
                    <div class="form-group">
                        {!! Form::label('name','Name : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::text('name', isset($group) && isset($group->name) ? $group->name : null, ['class' => 'form-control', 'placeholder' => 'Name', 'autocomplete' => 'off', 'required']) !!}
                            <span class="text-danger">{!! $errors->first('name') !!}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('role_id','Group Roles : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::select('role_id[]', $roles, isset($group) && isset($group->group_roles) ? $group->group_roles->pluck('role_id')->toArray() : null, ['class' => 'form-control', 'multiple']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="controls col-xs-12">
                            {!! Form::submit('Creat', ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                    
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection