<?php namespace App\Models\Group\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\Role\Role;

trait Relationship
{
	/**
	 * Relationship Mapping for Roles
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
	 */
	public function group_roles()
	{
	    return $this->belongsTo(Role::class);
	}
}