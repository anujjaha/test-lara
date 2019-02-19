<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission\Traits\Attribute\Attribute;
use App\Models\Permission\Traits\Relationship\Relationship;

class Permission extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'permissions';

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
