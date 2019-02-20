<?php namespace App\Repositories;


use Carbon\Carbon;

abstract class DbRepository extends Repository
{
	/**
	 * Get All
	 * 
	 * @param array $relations
	 * @return
	 */
    public function getAll($relations = array())
    {
    	if(isset($relations) && count($relations))
    	{
    		return $this->model->with($relations)->get();
    	}
    	
    	return $this->model->all();
    }

    /**
	 * Get By Id
	 *
	 * @param int $id
	 * @return
	 */
    public function getById($id = null)
    {
    	if(isset($id))
    	{
    		return $this->model->where('id', $id)->first();
    	}
    	
    	return false;
    }
}