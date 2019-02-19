<?php

namespace App\Repositories\Admin;

/**
 * Class EloquentUserRepository
 *
 * @author Anuj Jaha <er.anujjaha@gmail.com>
 */

use App\Models\User\User;
use App\Models\Group\Group;
use Illuminate\Http\Request;
use App\Repositories\DbRepository;
use App\Models\UserGroup\UserGroup;

class EloquentUserRepository extends DbRepository
{
    /**
     * User
     *
     * @var User
     */
    public $model;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model = new User();
    }

    /**
     * Get All Users
     * 
     * @return object
     */
    public function getAllUsers()
    {
        return $this->model->with(['user_groups', 'user_groups.group'])->where('id', '!=', 1)->get();
    }

    /**
     * Get User By Id
     * 
     * @param int $id
     * @return object|mixed
     */
    public function getUserById($id = null)
    {
        $user = $this->model->with('user_groups')->find($id);

        if(isset($user))
        {
            return $user;
        }

        return false;
    }

    /**
     * Create User
     *
     * @param Request $request
     * @return mixed
     */
    public function create($request)
    {
        $input = $request->all();

        return $this->model->create($input);
    }

    /**
     * Update User
     *
     * @param string|int $id
     * @param Array $input
     * @return bool|int|mixed
     */
    public function update($id, $input = array())
    {
        $model      = $this->model->find($id);
        $groupIds   = isset($input['group_id']) ? $input['group_id'] : false;
        $input      = $this->prepareInput($input);

        $status = $model->update($input);

        if($status && $groupIds)
        {
            $this->updateUserGroup($model, $groupIds);
        }

        return $model;
    }

    /**
     * Destroy User
     *
     * @param string|int $id
     * @return mixed
     * @throws GeneralException
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);

        if($model)
        {
            if($model->delete())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get User Groups
     * 
     * @return object
     */
    public function getUserGroups()
    {
        return Group::pluck('name', 'id')->toArray();
    }

    /**
     * Prepare Input
     * 
     * @param array $input
     * @return array
     */
    public function prepareInput($input = array())
    {
        return [
            'name'              => isset($input['name']) ? $input['name'] : '',
            'contact_number'    => isset($input['contact_number']) ? $input['contact_number'] : ''
        ];
    }

    /**
     * Update User Group
     * 
     * @param object $user
     * @param array $groupIds
     * @return bool
     */
    public function updateUserGroup($user = null, $groupIds = array())
    {
        $userGroups         = $user->user_groups;
        $userGroupData      = [];
        $processedGroupIds  = [];

        if($user && isset($groupIds))   
        {
            foreach($groupIds as $groupId)
            {
                $isExists               = $userGroups->where('group_id', $groupId);
                $processedGroupIds[]    = $groupId;

                if(isset($isExists) && count($isExists))
                {
                    continue;
                }
                else
                {
                    $userGroupData[] = [
                        'user_id'   => $user->id,
                        'group_id'  => $groupId
                    ];
                }
            }

            // Delete UnApplied Groups
            UserGroup::where('user_id', $user->id)
            ->whereNotIn('group_id', $processedGroupIds)
            ->delete();

            if(isset($userGroupData) && count($userGroupData))
            {
                UserGroup::insert($userGroupData);
            }
        }

        return true;
    }

    /**
     * Login With OTP
     * 
     * @param array $input
     * @return bool
     */
    public function loginWithOTP($input = array())
    {
        $user = $this->model->where([
            'contact_number'    => isset($input['mobile']) ? $input['mobile'] : '',
            'temp_otp'          => isset($input['otp']) ? $input['otp'] : ''
        ])->first();

        if(isset($user) && isset($user->id))
        {
            \Auth::loginUsingId($user->id, true);

            return true;
        }

        return false;
    }

    /**
     * Generate OTP
     * 
     * @param int|string $mobileNumber
     * @return bool
     */
    public function generateOTP($mobileNumber = null)
    {
        if($mobileNumber)
        {
            $user = $this->getUserByMobileNumber($mobileNumber);

            if(isset($user) && isset($user->id))
            {
                $otp = generateOTP();
                $user->temp_otp = $otp;
                $user->save();

                return $otp;
            }
        }

        return false;
    }

    /**
     * Get User By Mobile Number
     * 
     * @param int|string $mobileNumber
     * @return object
     */
    public function getUserByMobileNumber($mobileNumber = null)
    {
        if($mobileNumber)
        {
            return $this->model->where('contact_number', $mobileNumber)->first();
        }

        return false;
    }
}