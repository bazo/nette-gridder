<?php

namespace Gridder\Sources\ORM;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\EntityManager;



/**
 * Description of EntityIterator
 *
 * @author Martin
 */
class EntityIterator implements \Iterator
{

	/** @var IterableResult */
	private $iterator;

	/** @var EntityManager */
	private $em;
	private $current;



	public function __construct(\Iterator $iterator, $em)
	{
		$this->iterator = $iterator;
		$this->em = $em;
	}


	public function current()
	{
		$this->current = $this->iterator->current();
		return $this->current[0];
	}


	public function key()
	{
		return $this->iterator->key();
	}


	public function next()
	{
		$this->em->detach($this->current[0]);
		$this->iterator->next();
	}


	public function rewind()
	{
		$this->iterator->rewind();
	}


	public function valid()
	{
		return $this->iterator->valid();
	}


}
