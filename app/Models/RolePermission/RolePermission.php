<?php

namespace App\Models\RolePermission;

use Illuminate\Database\Eloquent\Model;
use App\Models\RolePermission\Traits\Attribute\Attribute;
use App\Models\RolePermission\Traits\Relationship\Relationship;

class RolePermission extends Model
{
    use Attribute, Relationship;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'role_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'permission_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
