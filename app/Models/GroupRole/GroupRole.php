<?php

namespace App\Models\GroupRole;

use Illuminate\Database\Eloquent\Model;
use App\Models\GroupRole\Traits\Attribute\Attribute;
use App\Models\GroupRole\Traits\Relationship\Relationship;

class GroupRole extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'group_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
