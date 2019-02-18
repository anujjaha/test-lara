<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role\Traits\Attribute\Attribute;
use App\Models\Role\Traits\Relationship\Relationship;

class Role extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'roles';

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
