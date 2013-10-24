<?php

namespace Gridder\Sources\Neo4j;

use Gridder\Gridder;
use Gridder\Helpers\Neo4j\Neo4jRowIterator;

/**
 * Neo4jSource
 *
 * @author Martin Bažík <martin@bazo.sk>
 */
class Neo4jCypherSource extends \Gridder\Sources\BaseSource
{

	protected $supportSFiltering = false;
	protected $supportsSorting = TRUE;
	private $sortingDirections = [
		Gridder::ORDER_BY_ASC => 'asc',
		Gridder::ORDER_BY_DESC => 'desc'
	];
	/** @var CypherQueryBuilder */
	private $qb;


	public function __construct(CypherQueryBuilder $qb)
	{
		$this->qb = $qb;
	}


	public function getRows()
	{
		return new Neo4jRowIterator($this->qb->execute());
		
	}


	public function applySorting(array $sorting = NULL)
	{
		if (!empty($sorting)) {
			foreach ($sorting as $sort) {
				$this->applySort($sort);
			}
		}
		return $this;
	}

	private function applySort(array $sort)
	{
		$column = key($sort);
		$order = $this->sortingDirections[$sort[$column]];
		$this->qb->order("$column $order");
	}

	public function applyFilters($filters)
	{
		return $this;
	}


	public function limit($offset, $limit)
	{

		return $this;
	}


	public function getTotalCount()
	{
		return $this->qb->execute()->count();
	}


}

