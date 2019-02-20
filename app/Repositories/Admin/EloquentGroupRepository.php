<?php

namespace App\Repositories\Admin;

/**
 * Class EloquentGroupRepository
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\User\User;
use App\Models\Role\Role;
use App\Models\Group\Group;
use Illuminate\Http\Request;
use App\Repositories\DbRepository;
use App\Models\UserGroup\UserGroup;
use App\Models\GroupRole\GroupRole;

class EloquentGroupRepository extends DbRepository
{
    /**
     * Group
     *
     * @var Group
     */
    public $model;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new Group();
    }

    /**
     * Get All Groups
     * 
     * @return object
     */
    public function getAllGroups()
    {
        return $this->model->with(['group_roles', 'group_roles.role'])->get();
    }

    /**
     * Create Group
     *
     * @param array $input
     * @return mixed
     */
    public function create($input = array())
    {
        $model = $this->model->create($input);

        if($model)
        {
            $this->attachRoles($model, $input['role_id']);

            return $model;
        }

        return false;
    }

    /**
     * Attach Roles
     * 
     * @param object $model
     * @param array $roleIds
     * @return bool
     */
    public function attachRoles($model, $roleIds = array())
    {
        if($model && isset($roleIds) && count($roleIds))
        {
            $groupRoleData = [];

            foreach($roleIds as $roleId)   
            {
                $groupRoleData[] = [
                    'group_id'  => $model->id,
                    'role_id'   => $roleId
                ];
            }

            if(count($groupRoleData))
            {
                GroupRole::insert($groupRoleData);
            }
        }
    }

    /**
     * Destroy
     *
     * @param string|int $id
     * @return bool|int|mixed
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);

        if($model)
        {
            return $model->delete();
        }

        return false;
    }

    /**
     * Get All Roles
     * 
     * @return array
     */
    public function getAllRoles()
    {
        return Role::pluck('name', 'id')->toArray();
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return array
     */
    public function getById($id = null)
    {
        return Group::with(['group_roles', 'group_roles.role'])->where('id', $id)->first();
    }
}