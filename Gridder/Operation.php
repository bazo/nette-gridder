<?php

namespace Gridder;

use Nette\Callback;
use \Closure;
use Gridder\Exception;

/**
 * Description of Operation
 *
 * @author Martin
 */
class Operation
{

	private
			$name,
			$alias,
			$callback,
			$ajax = false

	;

	private function verifyCallback($callback)
	{
		if($callback != null)
		{
			if(!($callback instanceof Callback or $callback instanceof Closure))
			{
				throw new Exception(sprintf('Callback must be an instance of Callback or Closure, %s given', get_class($callback)));
			}
		}
	}

	public function __construct($name, $callback = null)
	{
		$this->name = $name;
		$this->verifyCallback($callback);
		$this->callback = $callback;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function setCallback($callback)
	{
		$this->verifyCallback($callback);
		$this->callback = $callback;
		return $this;
	}

	public function getAlias()
	{
		if($this->alias != null)
		{
			return $this->alias;
		}
		else
		{
			return $this->name;
		}
	}

	public function setAlias($alias)
	{
		$this->alias = $alias;
		return $this;
	}

	public function execute($recordIds, $records)
	{
		$callback = $this->callback;
		return $callback($recordIds, $records);
	}
	
	public function getAjax()
	{
		return $this->ajax;
	}

	public function setAjax($ajax)
	{
		$this->ajax = $ajax;
		return $this;
	}
	
	public function isAjax()
	{
		return $this->ajax;
	}
}