<?php

namespace App\Repositories\Admin;

/**
 * Class EloquentRoleRepository
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
use App\Models\Permission\Permission;
use App\Models\RolePermission\RolePermission;

class EloquentRoleRepository extends DbRepository
{
    /**
     * Role
     *
     * @var Role
     */
    public $model;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new Role();
    }

    /**
     * Create Role
     *
     * @param array $input
     * @return mixed
     */
    public function create($input = array())
    {   
        $model = $this->model->create($input);

        if($model)
        {
            $this->attachPermissions($model, $input['permission_id']);

            return $model;
        }

        return false;
    }

    /**
     * Update
     * 
     * @param int $id
     * @param array $input
     * @return bool
     */
    public function update($id, $input)
    {
        $model          = $this->model->find($id);
        $permissionIds  = isset($input['permission_id']) ? $input['permission_id'] : false;
        $input          = $this->prepareInput($input);

        $status = $model->update($input);

        if($status && $permissionIds)
        {
            $this->updateRolePermissions($model, $permissionIds);
        }

        return $model;
    }

    /**
     * Prepare Input
     * 
     * @param array $input
     * @return array
     */
    public function prepareInput($input = array())
    {
        if(isset($input) && count($input))
        {
            return [
                'name' => isset($input['name']) ? $input['name'] : 'Default'
            ];
        }
    }

    /**
     * Attach Permissions
     * 
     * @param object $model
     * @param array $roleIds
     * @return bool
     */
    public function attachPermissions($model, $permissionIds = array())
    {
        if($model && isset($permissionIds) && count($permissionIds))
        {
            $rolePermissionData = [];

            foreach($permissionIds as $permissionId)   
            {
                $rolePermissionData[] = [
                    'role_id'       => $model->id,
                    'permission_id' => $permissionId
                ];
            }

            if(count($rolePermissionData))
            {
                RolePermission::insert($rolePermissionData);
            }
        }
    }

    /**
     * Update Role Permissions
     * 
     * @param object $role
     * @param array $permissionIds
     * @return bool
     */
    public function updateRolePermissions($role = null, $permissionIds = array())
    {
        $rolePermissions         = $role->role_permissions;
        $rolePermissionData      = [];
        $processedPermissionIds  = [];

        if($role && isset($permissionIds))   
        {
            foreach($permissionIds as $permissionId)
            {
                $isExists                   = $rolePermissions->where('permission_id', $permissionId);
                $processedPermissionIds[]   = $permissionId;

                if(isset($isExists) && count($isExists))
                {
                    continue;
                }
                else
                {
                    $rolePermissionData[] = [
                        'role_id'       => $role->id,
                        'permission_id' => $permissionId
                    ];
                }
            }

            // Delete UnApplied Groups
            RolePermission::where('role_id', $role->id)
            ->whereNotIn('permission_id', $processedPermissionIds)
            ->delete();

            if(isset($rolePermissionData) && count($rolePermissionData))
            {
                RolePermission::insert($rolePermissionData);
            }
        }

        return true;
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
     * Get All Permissions
     * 
     * @return array
     */
    public function getAllPermissions()
    {
        return Permission::pluck('name', 'id')->toArray();
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return array
     */
    public function getById($id = null)
    {
        return Role::with(['role_permissions', 'role_permissions.permission'])->where('id', $id)->first();
    }
}