@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">User List</div>
                
                <div class="card-body">
                    {!! Form::open(['route' => ['admin.user.update', $user->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post', 'id' => 'admin-update-user', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data']) !!}
                    
                    <div class="form-group">
                        {!! Form::label('name','Name : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::text('name', isset($user) && isset($user->name) ? $user->name : null, ['class' => 'form-control', 'placeholder' => 'Name', 'autocomplete' => 'off', 'required']) !!}
                            <span class="text-danger">{!! $errors->first('name') !!}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('name','Email Id :', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::text('email', isset($user) && isset($user->email) ? $user->email : null, ['class' => 'form-control', 'placeholder' => 'Email', 'autocomplete' => 'off', 'disabled']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact_number','Contact Number : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::text('contact_number', isset($user) && isset($user->contact_number) ? $user->contact_number : null, ['class' => 'form-control', 'placeholder' => 'Contact Number', 'autocomplete' => 'off', 'required']) !!}
                            <span class="text-danger">{!! $errors->first('contact_number') !!}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('group_id','User Groups : *', array('class' => 'control-label col-xs-12 col-sm-12 col-md-4 col-lg-3')) !!}
                        <div class="controls col-xs-12 col-sm-10 col-md-8 col-lg-9">
                            {!! Form::select('group_id[]', $groups, isset($user) && isset($user->user_groups) ? $user->user_groups->pluck('group_id')->toArray() : null, ['class' => 'form-control', 'multiple']) !!}
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
