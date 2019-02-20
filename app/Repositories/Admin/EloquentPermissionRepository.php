<?php

namespace App\Repositories\Admin;

/**
 * Class EloquentPermissionRepository
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use Illuminate\Http\Request;
use App\Models\Permission\Permission;
use App\Repositories\DbRepository;
use App\Models\RolePermission\RolePermission;

class EloquentPermissionRepository extends DbRepository
{
    /**
     * Permission
     *
     * @var Permission
     */
    public $model;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new Permission();
    }

    /**
     * Create Permission
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
            $rolePermissionData = [];

            foreach($roleIds as $roleId)   
            {
                $rolePermissionData[] = [
                    'permission_id' => $model->id,
                    'role_id'       => $roleId
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
     * @param object $permission
     * @param array $roleIds
     * @return bool
     */
    public function updateRolePermissions($permission = null, $roleIds = array())
    {
        $rolePermissions         = $permission->role_permissions;
        $rolePermissionData      = [];
        $processedPermissionIds  = [];

        if($permission && isset($roleIds))   
        {
            foreach($roleIds as $roleId)
            {
                $isExists                   = $rolePermissions->where('role_id', $roleId);
                $processedPermissionIds[]   = $roleId;

                if(isset($isExists) && count($isExists))
                {
                    continue;
                }
                else
                {
                    $rolePermissionData[] = [
                        'permission_id' => $permission->id,
                        'role_id'       => $roleId
                    ];
                }
            }

            // Delete UnApplied Groups
            RolePermission::where('permission_id', $permission->id)
            ->whereNotIn('role_id', $processedPermissionIds)
            ->delete();

            if(isset($rolePermissionData) && count($rolePermissionData))
            {
                RolePermission::insert($rolePermissionData);
            }
        }

        return true;
    }

    /**
     * Update Permission
     * 
     * @param int $id
     * @param array $input
     * @return bool
     */
    public function update($id, $input)
    {
        $model          = $this->model->find($id);
        $permissionIds  = isset($input['role_id']) ? $input['role_id'] : false;
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
     * Get Permission WithRoles
     * 
     * @param int $id
     * @return object
     */
    public function getPermissionWithRoles($id)
    {
        return $this->model->with('role_permissions')->where('id', $id)->first();
    }
}