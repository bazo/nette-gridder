<?php

namespace Gridder\Sources\ORM;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gridder\Exception;
use Gridder\Filters\FilterObject;
use Gridder\Gridder;
use Gridder\Sources\BaseSource;
use Gridder\Filters\Filter;



/**
 * Description of EntitySource
 *
 * @author Martin
 */
class NativeQuerySource extends BaseSource
{

	const HYDRATION_SIMPLE = Query::HYDRATE_SIMPLEOBJECT;
	const HYDRATION_COMPLEX = Query::HYDRATE_OBJECT;
	const HYDRATION_ARRAY = Query::HYDRATE_ARRAY;


	private $sortingDirections = [
		Gridder::ORDER_BY_ASC => 'asc',
		Gridder::ORDER_BY_DESC => 'desc'
	];

	/** @var \Doctrine\ORM\NativeQuery */
	protected $query;
	protected $hydrationMode;
	protected $supportSFiltering = TRUE;


	public function __construct(\Doctrine\ORM\NativeQuery $query, $hydrationMode = self::HYDRATION_SIMPLE)
	{
		$this->hydrationMode = $hydrationMode;
		$this->query = $query;
	}


	


	private function extractMetadata()
	{
		return [];
		static $metadata = NULL;

		if (is_null($metadata)) {

			$selectExpressions = $this->query->getAST()->selectClause->selectExpressions;
			foreach ($selectExpressions as $expr) {
				$expression = $expr->expression;

				if (!is_object($expression)) {
					$metadata['prefix'] = $expression;
				} else {
					$column = $expr->fieldIdentificationVariable !== NULL ? $expr->fieldIdentificationVariable : $expression->field;
					$value = $expr->fieldIdentificationVariable !== NULL ? $expr->fieldIdentificationVariable : $expression->identificationVariable . '.' . $expression->field;
					$metadata[$column] = $value;
				}
			}
		}
		$this->metadata = $metadata;

		return $metadata;
	}


	public function getRows()
	{

		$result = $this->query->iterate([], $this->hydrationMode);

		if ($this->hydrationMode === self::HYDRATION_ARRAY) {
			return new ArrayResultIterator($result);
		}
		return new EntityIterator($result, $this->query->getEntityManager());
	}


	public function getTotalCount()
	{
		$result = $this->query->getResult(Query::HYDRATE_ARRAY);
		return count($result);
	}


	public function limit($offset, $limit)
	{
		return $this;
	}


	public function getRecordsByIds($ids)
	{
		
	}


	public function applyFilters($filters)
	{
		return $this;
		if ($filters == null) {
			return $this;
		}

		foreach ($filters as $filter) {
			if ($filter->notEmpty()) {
				$this->applyFilter($filter);
			}
		}

		return $this;
	}


	protected function applyFilter(FilterObject $filter)
	{
		
		static $fieldCounter = 0;

		$fieldPlaceholder = 'field' . ( ++$fieldCounter);

		$this->extractMetadata();
		$value = $filter->getValue();
		$field = isset($this->metadata[$filter->getFilterFieldName()]) ? $this->metadata[$filter->getFilterFieldName()] : $filter->getFilterFieldName();
		if (is_array($value)) {
			switch ($filter->getOperator()) {
				case Filter::IN:
					$this->query->field($filter->getField())->in($filter->getValue());
					break;

				case Filter::RANGE:
					$date1 = $filter->getValue()['from']->format('Y-m-d');
					$date2 = $filter->getValue()['to']->format('Y-m-d');

					$this->query->andWhere(sprintf("%s BETWEEN '%s' AND '%s'", $field, $date1, $date2));

					break;
			}
		} else {
			switch ($filter->getOperator()) {
				case Filter::LIKE:

					$valuePlaceholder = $fieldPlaceholder . 'Value';

					$this->query->andHaving(sprintf("%s LIKE :%s", $field, $valuePlaceholder))
							->setParameter($valuePlaceholder, '%' . $filter->getValue() . '%')
					;

					break;

				case Filter::EQUAL:
					$valuePlaceholder = $fieldPlaceholder . 'Value';

					$this->query->andWhere(sprintf("%s = :%s", $valuePlaceholder))
							->setParameter($valuePlaceholder, $filter->getValue())
					;

					break;

				case Filter::REFERENCES:
					break;
			}
		}
	}


	public function applySorting(array $sorting = NULL)
	{
		return $this;
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
		$this->query->orderBy($column, $order);
	}


	public function getMetadata()
	{
		return $this->extractMetadata();
	}


}
