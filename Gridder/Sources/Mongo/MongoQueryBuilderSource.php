<?php

namespace Gridder\Sources\Mongo;


use Gridder\Gridder;
use Doctrine\MongoDB\Query\Builder;
use Gridder\Filters\FilterObject;
use Gridder\Filters\Filter;

/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class MongoQueryBuilderSource extends \Gridder\Sources\BaseSource
{

	/** @var Builder */
	protected $builder;

	/** @var array */
	protected $sortingDirections = [
		Gridder::ORDER_BY_ASC	 => 'asc',
		Gridder::ORDER_BY_DESC	 => 'desc'
	];

	/** @var string */
	protected $primaryKey;

	/** @var string */
	protected $filterChainMode = 'and';

	/** @var bool */
	protected $supportSFiltering = TRUE;

	/** @var bool */
	protected $supportsSorting = TRUE;

	public function __construct(Builder $builder)
	{
		$this->builder = $builder;
	}


	public function prime($referenceField)
	{
		$this->builder->field($referenceField)->prime();
		return $this;
	}


	public function applyFilters($filters)
	{
		if ($filters === NULL) {
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
		$value = $filter->getValue();
		if (is_array($value)) {
			switch ($filter->getOperator()) {
				case Filter::IN:
					$this->builder->field($filter->getField())->in($filter->getValue());
					break;

				case Filter::RANGE:
					$this->builder->field($filter->getField())->gte($filter->getFrom())->lte($filter->getTo());
					break;
			}
		} else {
			switch ($filter->getOperator()) {
				case Filter::LIKE:
					$this->builder->field($filter->getField())->equals(new \MongoRegex('/.*' . $filter->getValue() . '.*/i'));
					break;

				case Filter::EQUAL:
					$this->builder->field($filter->getField())->equals($filter->getValue());
					break;

				case Filter::REFERENCES:
					break;
			}
		}
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
		$column	 = key($sort);
		$order	 = $this->sortingDirections[$sort[$column]];
		$this->builder->sort($column, $order);
	}


	public function limit($offset, $limit)
	{
		if ($offset < 0) {
			$offset = 0;
		}
		$this->builder->skip($offset)->limit($limit);
		return $this;
	}


	public function getRows()
	{
		return $this->builder->getQuery()->execute();
	}


	public function getTotalCount()
	{
		return $this->builder->getQuery()->count(TRUE);
	}


	public function getRecordsByIds($ids)
	{
		return $this->builder->field('id')->in($ids)->getQuery()->execute();
	}


}
