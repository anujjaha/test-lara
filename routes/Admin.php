<?php

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function()
{
	
	/**
	 * Admin / Groups
	 */
	Route::resource('groups', 'AdminGroupController', [
	    'except' => ['index', 'show', 'store', 'destroy']
	]);

	Route::any('groups', 'AdminGroupController@index')->name('groups.index');
	Route::post('groups/store', 'AdminGroupController@store')->name('groups.store');
	Route::get('groups/{id}/show', 'AdminGroupController@show')->name('groups.show');
	Route::get('groups/{id}/destroy', 'AdminGroupController@destroy')->name('groups.destroy');

	Route::get('/users', 'AdminUserController@users')->name('users');
	Route::get('/user/{id}/edit', 'AdminUserController@edit')->name('edit-user');
	Route::post('/user/{id}/update', 'AdminUserController@update')->name('user.update');
	Route::get('/user/{id}/destroy', 'AdminUserController@destroy')->name('delete-user');
});