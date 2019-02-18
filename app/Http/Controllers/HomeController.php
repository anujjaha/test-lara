<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserGroup\UserGroup;
use App\Models\User\User;

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
        $data = UserGroup::where('user_id', 1)->with(['group', 'group.group_roles'])->get();
        dd($data);
        //group_roles
        $user = User::where('id', Auth()->user()->id)->with(['user_groups', 'user_groups.group'])->first();
        dd($user);
        dd($user->user_groups->pluck('group_id')->toArray());
        return view('home');
    }
}
