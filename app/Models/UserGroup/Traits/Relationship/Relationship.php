<?php namespace App\Models\UserGroup\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\Group\Group;
use App\Models\User\User;

trait Relationship
{
	/**
	 * Relationship Mapping for User
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
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