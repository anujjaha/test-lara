<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Admin\EloquentUserRepository;

class DashboardController extends Controller
{
    /**
     * User Repository
     *
     * @var EloquentUserRepository
     */
    protected $repository;

    /**
     * RredirectTo
     * 
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new EloquentUserRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function generateOTP(Request $request)
    {
        $mobile = $request->get('mobileNumber');
        $otp    = $this->repository->generateOTP($mobile);

        if($otp)
        {
            return response()->json([
                'status'   => true,
                'otp'       => $otp
            ]);
        }

        return response()->json([
                'status' => false
            ]);
    }

    /**
     * Login With OTP
     * 
     * @param  Request $request
     * @return view
     */
    public function loginWithOTP(Request $request)
    {
        $status = $this->repository->loginWithOTP($request->all());

        if($status)
        {
            return redirect('/');
        }

        return redirect()->back();
    }

    /**
     * Logout
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function logout()
    {
        logoutUser();
        
        auth()->logout();
        
        return redirect('/');
    }
}