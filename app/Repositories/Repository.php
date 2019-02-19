<?php namespace App\Repositories;

/**
 * Class Repository
 *
 * @package App\Repositories
 */

abstract class Repository extends BaseRepository
{
	/**
     * Get All
     *
	 * @return mixed
	 */
	public function getAll()
	{
		return $this->query()->get();
	}

	/**
     * Get Count
     *
	 * @return mixed
	 */
	public function getCount()
    {
		return $this->query()->count();
	}

	/**
     * Find
     *
	 * @param $id
	 * @return mixed
	 */
	public function find($id)
	{
		return $this->query()->find($id);
	}
}