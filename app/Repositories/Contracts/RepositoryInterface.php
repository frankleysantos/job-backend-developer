<?php namespace App\Repositories\Contracts;

interface RepositoryInterface{
	
	public function getAll();

	public function getEntity($id, $name);

	public function store($request);

	public function update($request);

	public function delete($id);
}