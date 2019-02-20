<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Client\Dashboard\ViewRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * My Profile
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile(ViewRequest $request)
    {
        $user = Auth()->user();
        return view('profile')->with(compact('user'));
    }

    /**
     * Delete Account
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteAccount(ViewRequest $request)
    {
        $user = Auth()->user();
         
        $user->delete();

        Auth()->logout();
        
        return redirect('/');
    }
}
