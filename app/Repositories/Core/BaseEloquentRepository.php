<?php namespace App\Repositories\Core;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Exceptions\NotEntityDefined;


class BaseEloquentRepository implements RepositoryInterface
{
    protected $entity;
    public function __construct()
    {
        $this->entity = $this->resolveEntity();
    }
	public function getAll()
	{
		return $this->entity->all();
	}

	public function getEntity($id = null, $name = null)
	{

		return $this->entity
				->when($name, function($query) use ($name){
					return $query->where('name', $name);
				})
				->when($id, function($query) use ($id){
					return $query->where('id', $id);
				})
				->first();
	}

	public function store($request)
	{
		return $this->entity->create($request);
	}


	public function update($request)
	{
		return $this->entity->where('id', $request->id)->update($request->except('id'));
	}

	public function delete($id)
	{
		return $this->entity->where('id', $id)->delete();
	}

    public function resolveEntity(){
        if(!method_exists($this, 'entity')){
            throw new NotEntityDefined();
        }
        return app($this->entity());
    }

}