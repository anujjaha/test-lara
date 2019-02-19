<?php

/**
 * Global helpers file with misc functions
 *
 */

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Debug\HtmlDumper;
use Illuminate\Support\Facades\DB;
use App\Models\UserGroup\UserGroup;
use App\Models\GroupRole\GroupRole;
use App\Models\Permission\Permission;

if(!function_exists('userPermissions'))
{
    /**
     * Get Dashboard Redirect Based on Permission
     *
     * @param string $default
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function userPermissions()
    {
        if(session()->has('user_permissions'))
        {
            return session()->pull('user_permissions');
        }
        else
        {
            $currentUser    = Auth()->user();
            $userGroupIds   = UserGroup::where('user_id', $currentUser->id)->with(['group'])->pluck('group_id')->toArray();
            $groupRoles     = GroupRole::whereIn('group_id', $userGroupIds)->with(['role', 'role.role_permissions'])->get();
            $permissions    = [];
            $permissionIds  = [];

            if(isset($groupRoles) && count($groupRoles))
            {
                foreach($groupRoles as $groupRole)
                {
                    $permissionIds = array_merge($permissionIds, $groupRole->role->role_permissions->pluck('permission_id')->toArray());
                }
            }

            if(isset($permissionIds) && count($permissionIds))
            {
                $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
            }

            session()->push('user_permissions', $permissions);
            
            return $permissions;
        }
    }
}

if(!function_exists('isAdmin'))
{
    /**
     * Get Dashboard Redirect Based on Permission
     *
     * @param string $default
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function isAdmin()
    {
        $currentUser    = Auth()->user();   
        $userGroups     = UserGroup::where('user_id', $currentUser->id)->with(['group'])->get();    
        $isAdminGroup   = $userGroups->where('group.name', 'admin')->first();

        if(isset($isAdminGroup))
        {
            return true;
        }

        return false;
    }
}

if(!function_exists('generateOTP'))
{
    /**
     * Generate OTP
     * 
     * @return string
     */
    function generateOTP()
    {
       return rand(11111, 99999);
    }
}