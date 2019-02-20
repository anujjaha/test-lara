@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Edit Role</div>
                
                <div class="card-body">
                    {!! Form::model($role, ['route' => ['admin.roles.update', $role->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH', 'id' => 'admin-edit-role', 'files' => true]) !!}
                    
                    <div class="form-group">
                        {!! Form::label('name','Name : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::text('name', isset($role) && isset($role->name) ? $role->name : null, ['class' => 'form-control', 'placeholder' => 'Name', 'autocomplete' => 'off', 'required']) !!}
                            <span class="text-danger">{!! $errors->first('name') !!}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('role_id','Role Permissions : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::select('permission_id[]', $permissions, isset($role) && isset($role->role_permissions) ? $role->role_permissions->pluck('permission_id')->toArray() : null, ['class' => 'form-control', 'multiple']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="controls col-xs-12">
                            {!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                    
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection