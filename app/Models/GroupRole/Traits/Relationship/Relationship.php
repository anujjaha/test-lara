<?php namespace App\Models\GroupRole\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\Group\Group;
use App\Models\Role\Role;

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
	 * Relationship Mapping for Group
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function group()
	{
	    return $this->belongsTo(Group::class, 'group_id');
	}
}