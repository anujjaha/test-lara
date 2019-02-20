<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EloquentRoleRepository;
use App\Http\Requests\Admin\Group\DeleteRequest;
use App\Http\Requests\Admin\Group\UpdateRequest;

class AdminRoleController extends Controller
{
    /**
     * Role Repository
     *
     * @var EloquentRoleRepository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new EloquentRoleRepository;
    }

    /**
     * Roles
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $roles = $this->repository->getAll();
        
        return view('admin.role.list')->with(compact('roles'));
    }

    /**
     * Create Role
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        $permissions = $this->repository->getAllPermissions();

        return view('admin.role.create')->with(compact('permissions'));
    }

    /**
     * Edit Role
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id, Request $request)
    {
        $role           = $this->repository->getById($id);
        $permissions    = $this->repository->getAllPermissions();
        
        if($role)
        {
            return view('admin.role.edit')->with(compact('role', 'permissions'));
        }

        return redirect()->route('admin.roles.index')->withFlashDanger(__('No Role Found!'));
    }

    /**
     * View Role
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id, Request $request)
    {
        $role = $this->repository->getById($id);

        return view('admin.role.show')->with(compact('role'));
    }

    /**
     * Store Role
     * 
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $status = $this->repository->create($request->all());

        if($status)
        {
            return redirect()->route('admin.roles.index')->withFlashSuccess(__('New Role Created Successfully'));
        }
        
        return redirect()->route('admin.roles.index')->withFlashDanger(__('Unable to Create Role!'));
    }

    /**
     * Update Role
     * 
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $status = $this->repository->update($id, $request->all());

        if($status)
        {
            return redirect()->route('admin.roles.index')->withFlashSuccess(__('Role Updated Successfully'));
        }
        
        return redirect()->route('admin.roles.index')->withFlashDanger(__('Unable to Update Role!'));
    }

    /**
     * Delete Role
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function destroy($id, DeleteRequest $request)
    {
        $status = $this->repository->destroy($id);

        if($status)
        {
            return redirect()->route('admin.roles.index')->withFlashSuccess(__('Role Deleted Successfully'));
        }

        return redirect()->route('admin.roles.index')->withFlashDanger(__('Something Went Wrong!'));
    }
}
