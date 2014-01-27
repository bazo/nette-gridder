<?php

namespace Gridder\Sources\ORM;

use Doctrine\ORM\Internal\Hydration\IterableResult;

/**
 * Description of ArrayResultIterator
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class ArrayResultIterator implements \Iterator
{
	/** @var IterableResult */
	private $iterableResult;

	public function __construct(IterableResult $iterableResult)
	{
		$this->iterableResult = $iterableResult;
	}


	public function current()
	{
		$current = $this->iterableResult->current();
		return current($current);
	}


	public function key()
	{
		return $this->iterableResult->key();
	}


	public function next()
	{
		return $this->iterableResult->next();
	}


	public function rewind()
	{
		return $this->iterableResult->rewind();
	}


	public function valid()
	{
		return $this->iterableResult->valid();
	}


}
