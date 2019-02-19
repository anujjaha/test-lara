<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EloquentUserRepository;

class AdminController extends Controller
{
    /**
     * User Repository
     *
     * @var EloquentUserRepository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $this->repository = new EloquentUserRepository;
    }

    /**
     * My Profile
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = Auth()->user();
        return view('profile')->with(compact('user'));
    }
}
