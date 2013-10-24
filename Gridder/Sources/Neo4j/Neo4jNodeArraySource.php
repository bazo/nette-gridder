<?php

namespace Gridder\Sources\Neo4j;

/**
 * Neo4jSource
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Neo4jNodeArraySource extends \Gridder\Sources\BaseSource
{

	private $rows = [];


	function __construct($rows)
	{
		$this->rows = $rows;
	}


	public function getRows()
	{
		return $this->rows;
	}


	public function applySorting(array $sorting = NULL)
	{
		return $this;
	}


	public function getTotalCount()
	{
		return count($this->rows);
	}


}

