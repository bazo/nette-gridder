<?php

namespace Gridder\Persisters;

/**
 * Description of Persister
 *
 * @author Martin
 * @property filters
 * @property itemsPerPage
 * @property recordCheckboxes
 * @property page
 * @property totalPages
 */
abstract class BasePersister implements Persister
{

	public function save($key, $data)
	{
		
	}


	public function get($key)
	{
		
	}


	public function keyExists($key)
	{
		
	}


	public function delete($key)
	{
		
	}


	public function reset()
	{
		
	}


	public function __set($key, $data)
	{
		$this->save($key, $data);
	}


	public function __get($key)
	{
		return $this->get($key);
	}


	public function __isset($key)
	{
		return $this->keyExists($key);
	}


	public function __unset($key)
	{
		return $this->delete($key);
	}


	public function getStorage()
	{
		return $this->storage;
	}


}

