<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\EloquentGroupRepository;
use App\Http\Requests\Admin\Group\DeleteRequest;
use App\Http\Requests\Admin\Group\UpdateRequest;

class AdminGroupController extends Controller
{
    /**
     * Group Repository
     *
     * @var EloquentGroupRepository
     */
    protected $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new EloquentGroupRepository;
    }

    /**
     * Groups
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $groups = $this->repository->getAllGroups();
        
        return view('admin.group.list')->with(compact('groups'));
    }

    /**
     * Create Group
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create(Request $request)
    {
        $roles = $this->repository->getAllRoles();

        return view('admin.group.create')->with(compact('roles'));
    }

    /**
     * View Group
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id, Request $request)
    {
        $group = $this->repository->getById($id);

        return view('admin.group.show')->with(compact('group'));
    }

    /**
     * Store Group
     * 
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $status = $this->repository->create($request->all());

        if($status)
        {
            return redirect()->route('admin.groups.index')->withFlashSuccess(__('Group Created Successfully'));
        }
        
        return redirect()->route('admin.groups.index')->withFlashDanger(__('Unable to Create Group!'));
    }

    /**
     * Delete Group
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function destroy($id, DeleteRequest $request)
    {
        $status = $this->repository->destroy($id);

        if($status)
        {
            return redirect()->route('admin.groups.index')->withFlashSuccess(__('Group Deleted Successfully'));
        }

        return redirect()->route('admin.groups.index')->withFlashDanger(__('Something Went Wrong!'));
    }
}
