<?php namespace App\Models\Role\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\RolePermission\RolePermission;

trait Relationship
{
	/**
     * Relationship Mapping for Role Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
public function role_permissions()
    {
    	return $this->hasMany(RolePermission::class, 'role_id');
    }
}