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

	/**
	 * Admin / Roles
	 */
	Route::resource('roles', 'AdminRoleController', [
	    'except' => ['index', 'show', 'store', 'destroy']
	]);

	Route::any('roles', 'AdminRoleController@index')->name('roles.index');
	Route::post('roles/store', 'AdminRoleController@store')->name('roles.store');
	Route::get('roles/{id}/show', 'AdminRoleController@show')->name('roles.show');
	Route::get('roles/{id}/destroy', 'AdminRoleController@destroy')->name('roles.destroy');

	/**
	 * Admin / Permissions
	 */
	Route::resource('permissions', 'AdminPermissionController', [
	    'except' => ['index', 'show', 'store', 'destroy']
	]);

	Route::any('permissions', 'AdminPermissionController@index')->name('permissions.index');
	Route::post('permissions/store', 'AdminPermissionController@store')->name('permissions.store');
	Route::get('permissions/{id}/show', 'AdminPermissionController@show')->name('permissions.show');
	Route::get('permissions/{id}/destroy', 'AdminPermissionController@destroy')->name('permissions.destroy');

	Route::get('/users', 'AdminUserController@users')->name('users');
	Route::get('/user/{id}/edit', 'AdminUserController@edit')->name('edit-user');
	Route::post('/user/{id}/update', 'AdminUserController@update')->name('user.update');
	Route::get('/user/{id}/destroy', 'AdminUserController@destroy')->name('delete-user');
});