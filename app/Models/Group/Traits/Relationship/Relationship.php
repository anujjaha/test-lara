<?php namespace App\Models\Group\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\GroupRole\GroupRole;

trait Relationship
{
	/**
	 * Relationship Mapping for Roles
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
	 */
	public function group_roles()
	{
	    return $this->hasMany(GroupRole::class, 'group_id');
	}
}