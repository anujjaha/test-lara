<?php namespace App\Models\User\Traits\Relationship;

/**
 * Trait Relationship
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\UserGroup\UserGroup;

trait Relationship
{
	/**
     * Relationship Mapping for Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_groups()
    {
    	return $this->hasMany(UserGroup::class, 'user_id');
    }
}