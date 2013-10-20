<?php

namespace Gridder;

/**
 * Gridderable
 *
 * @author Martin
 */
trait Gridderable
{

	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}


	public function offsetSet($offset, $value)
	{
		throw new BadMethodCallException("Array access of class " . get_class($this) . " is read-only!");
	}


	public function offsetGet($offset)
	{
		return $this->$offset;
	}


	public function offsetUnset($offset)
	{
		throw new BadMethodCallException("Array access of class " . get_class($this) . " is read-only!");
	}


}

