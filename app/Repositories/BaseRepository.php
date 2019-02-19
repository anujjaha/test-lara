<?php namespace App\Repositories;

/**
 * Class BaseRepository
 *
 */

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * Model
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        $saved = $model->save();

        return $saved;
    }

	/**
     * Force Delete
     *
     * @param Model $model
     * @return bool|null
     */
	public function forceDelete(Model $model)
	{
		$deleted = $model->forceDelete();

		if($deleted)
		{
			app('cache')->flush();
		}

		return $deleted;
	}

	/**
     * Query Instance
     *
	 * @return mixed
	 */
	protected function query()
	{
    
	}
}