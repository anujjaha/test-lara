<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EloquentUserRepository;
use App\Http\Requests\Admin\User\DeleteRequest;
use App\Http\Requests\Admin\User\UpdateRequest;

class AdminUserController extends Controller
{
    /**
     * Use Repository
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
        $this->repository = new EloquentUserRepository;
    }

    /**
     * Users
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        $users = $this->repository->getAllUsers();

        return view('admin.user.user-list')->with(compact('users'));
    }

    /**
     * Edit User
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id, Request $request)
    {
        $user   = $this->repository->getUserById($id);
        $groups = $this->repository->getUserGroups();

        if($user)
        {
            return view('admin.user.user-edit')->with(compact('user', 'groups'));
        }

        return redirect()->route('admin.users')->withFlashDanger(__('No User Found!'));
    }

    /**
     * Update User
     * 
     * @param int $id
     * @param UpdateRequest $request [description]
     * @return mixed
     */
    public function update($id, UpdateRequest $request)
    {
        $status = $this->repository->update($id, $request->all());

        if($status)
        {
            return redirect()->route('admin.users')->withFlashSuccess(__('User Updated Successfully'));
        }
        
        return redirect()->route('admin.users')->withFlashDanger(__('No User Found!'));
    }

    /**
     * Users
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function destroy($id, DeleteRequest $request)
    {
        $status = $this->repository->destroy($id);

        if($status)
        {
            return redirect()->route('admin.users')->withFlashSuccess(__('User Deleted Successfully'));
        }

        return redirect()->route('admin.users')->withFlashDanger(__('Something Went Wrong!'));
    }
}
