<?php

namespace Gridder\Sources\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gridder\Exception;
use Gridder\Gridder;
use Gridder\Sources\BaseSource;



/**
 * Description of EntitySource
 *
 * @author Martin
 */
class QueryBuilderSource extends BaseSource
{

	const HYDRATION_SIMPLE = Query::HYDRATE_SIMPLEOBJECT;
	const HYDRATION_COMPLEX = Query::HYDRATE_OBJECT;
	const HYDRATION_ARRAY = Query::HYDRATE_ARRAY;



	private $sortingDirections = [
		Gridder::ORDER_BY_ASC => 'asc',
		Gridder::ORDER_BY_DESC => 'desc'
	];

	/** @var QueryBuilder */
	protected $builder;
	protected $hydrationMode;



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

		$selectExpressions = $this->builder->getQuery()->getAST()->selectClause->selectExpressions;
		foreach ($selectExpressions as $expr) {
			$expression = $expr->expression;

			if (!is_object($expression)) {
				$this->metadata['prefix'] = $expression;
			} else {
				$column = $expr->fieldIdentificationVariable !== NULL ? $expr->fieldIdentificationVariable : $expression->field;
				$value = $expr->fieldIdentificationVariable !== NULL ? $expr->fieldIdentificationVariable : $expression->identificationVariable . '.' . $expression->field;
				$this->metadata[$column] = $value;
			}
		}

		if ($this->hydrationMode === self::HYDRATION_ARRAY) {
			return new ArrayResultIterator($result);
		}
		return new EntityIterator($result, $this->builder->getEntityManager());
	}


	public function getTotalCount()
	{
		$result = $this->builder->getQuery()->execute([], Query::HYDRATE_ARRAY);
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
		if ($sorting !== NULL) {
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
		$this->builder->orderBy($column, $order);
	}


}
