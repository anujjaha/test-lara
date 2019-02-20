<?php namespace App\Models\RolePermission\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\Role\Role;
use App\Models\Permission\Permission;

trait Relationship
{
	/**
	 * Relationship Mapping for Role
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function role()
	{
	    return $this->belongsTo(Role::class, 'role_id');
	}

	/**
	 * Relationship Mapping for Permission
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function permission()
	{
	    return $this->belongsTo(Permission::class, 'permission_id');
	}
}