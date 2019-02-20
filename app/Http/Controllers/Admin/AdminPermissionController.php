<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EloquentPermissionRepository;
use App\Repositories\Admin\EloquentRoleRepository;

class AdminPermissionController extends Controller
{
    /**
     * Permission Repository
     *
     * @var EloquentPermissionRepository
     */
    protected $repository;

    /**
     * Role Repository
     *
     * @var EloquentRoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository       = new EloquentPermissionRepository;
        $this->roleRepository   = new EloquentRoleRepository;
    }

    /**
     * Permission
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $permissions = $this->repository->getAll(['role_permissions', 'role_permissions.role']);

        return view('admin.permission.list')->with(compact('permissions'));
    }

    /**
     * Create Permission
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        $roles = $this->roleRepository->getAll();
        $roles = $roles->pluck('name', 'id')->toArray();

        return view('admin.permission.create')->with(compact('roles'));
    }

    /**
     * Edit Permission
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id, Request $request)
    {
        $permission = $this->repository->getPermissionWithRoles($id);
        $roles      = $this->roleRepository->getAll();
        $roles      = $roles->pluck('name', 'id')->toArray();

        if($permission)
        {
            return view('admin.permission.edit')->with(compact('permission', 'roles'));
        }

        return redirect()->route('admin.permission.index')->withFlashDanger(__('No Permission Found!'));
    }

    /**
     * View Permission
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id, Request $request)
    {
        $role = $this->repository->getById($id);

        return view('admin.role.show')->with(compact('role'));
    }

    /**
     * Store Permission
     * 
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $status = $this->repository->create($request->all());

        if($status)
        {
            return redirect()->route('admin.permissions.index')->withFlashSuccess(__('New Permission Created Successfully'));
        }
        
        return redirect()->route('admin.permissions.index')->withFlashDanger(__('Unable to Create Permission!'));
    }

    /**
     * Update Permission
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
            return redirect()->route('admin.permissions.index')->withFlashSuccess(__('Permissiono Updated Successfully'));
        }
        
        return redirect()->route('admin.permissions.index')->withFlashDanger(__('Unable to Update Permission!'));
    }

    /**
     * Delete Permission
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
