<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login-via-otp', function () {
    return view('auth.login-otp');
});

Route::get('login-with-otp', function()
{
	return view('auth.login-otp');
})->name('login-with-otp'); 

Route::post('login-otp', 'DashboardController@loginWithOTP')->name('login-otp'); 
Route::post('generate-otp', 'DashboardController@generateOTP')->name('generate-otp'); 
Route::get('user-logout', 'DashboardController@logout')->name('user-logout'); 

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/my-profile', 'HomeController@profile')->name('profile');
Route::get('/delete-account', 'HomeController@deleteAccount')->name('delete-account');

require (__DIR__ . '/Admin.php');

