<?php

namespace App\Models\UserGroup;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserGroup\Traits\Attribute\Attribute;
use App\Models\UserGroup\Traits\Relationship\Relationship;

class UserGroup extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
