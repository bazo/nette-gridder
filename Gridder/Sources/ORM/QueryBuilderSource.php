<?php

namespace Gridder\Sources\ORM;

use Gridder\Sources\BaseSource;
use Gridder\Exception;
use Doctrine\ORM\QueryBuilder;
use Gridder\Sources\ORM\EntityIterator;
use Doctrine\ORM\Query;

/**
 * Description of EntitySource
 *
 * @author Martin
 */
class QueryBuilderSource extends BaseSource
{

	/** @var QueryBuilder */
	protected $builder;
	protected $hydrationMode;



	const HYDRATION_SIMPLE = Query::HYDRATE_SIMPLEOBJECT;
	const HYDRATION_COMPLEX = Query::HYDRATE_OBJECT;
	const HYDRATION_ARRAY = Query::HYDRATE_ARRAY;



	public function __construct(QueryBuilder $queryBuilder, $hydrationMode = self::HYDRATION_SIMPLE)
	{
		$this->hydrationMode = $hydrationMode;
		$this->builder = $queryBuilder;
	}


	protected function setQueryBuilder(QueryBuilder $queryBuilder)
	{
		if ($queryBuilder->getType() != QueryBuilder::SELECT) {
			throw new Exception('Only QueryBuilder of type QueryBuilder::SELECT is accepted');
		}
	}


	public function getRows()
	{
		$query = $this->builder->getQuery();
		$result = $query->iterate([], $this->hydrationMode);

		if($this->hydrationMode === self::HYDRATION_ARRAY) {
			return new ArrayResultIterator($result);
		}
		return new EntityIterator($result, $this->builder->getEntityManager());
	}


	public function getTotalCount()
	{
		$result = $this->builder->getQuery()->execute([], \Doctrine\ORM\Query::HYDRATE_ARRAY);
		return count($result);
	}


	public function limit($offset, $limit)
	{
		$this->builder->setFirstResult($offset)
				->setMaxResults($limit);
		return $this;
	}


	public function getRecordsByIds($ids)
	{

	}


	public function applyFilters($filters)
	{
		if ($filters == null)
			return $this;
		return $this;
	}


	public function applySorting(array $sorting = NULL)
	{
		return $this;
	}


}
