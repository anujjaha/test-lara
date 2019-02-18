<?php

namespace App\Models\Group;

use Illuminate\Database\Eloquent\Model;
use App\Models\Group\Traits\Attribute\Attribute;
use App\Models\Group\Traits\Relationship\Relationship;

class Group extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
