<?php
namespace Gridder\Sources;
use Gridder\Gridder;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\MongoDB\Query\Builder;
use Gridder\Filters\FilterObject;
use Gridder\Filters\Filter;
/**
 * MongoQuerySource
 *
 * @author martin.bazik
 */
class MongoRepositorySource extends BaseSource
{
	private
		/** @var DocumentRepository */	
		$repository,
		
		/** @var Builder */	
		$builder,
			
		$defaultSort = null,
			
		$sortingDirections = array(
			Gridder::ORDER_BY_ASC => 'asc',
			Gridder::ORDER_BY_DESC => 'desc'
		)
	;
	
	protected
		$primaryKey,
		$filterChainMode = 'and',
		$supportSFiltering = true,
		$supportsSorting = true
	;
	
	public function __construct(DocumentRepository $repository)
	{
		$this->repository = $repository;
		$this->builder = $repository->createQueryBuilder()->eagerCursor(true);
	}
	
	public function prime($referenceField)
	{
		$this->builder->field($referenceField)->prime();
		return $this;
	}
	
	public function applyFilters($filters)
	{
		if($filters === null) return $this;
		foreach($filters as $filter)
		{
			if($filter->notEmpty())
			{
				$this->applyFilter($filter);
			}
		}
		return $this;
	}
	
	protected function applyFilter(FilterObject $filter)
	{
		$value = $filter->getValue();
		if(is_array($value))
		{
			switch($filter->getOperator())
			{
				case Filter::IN:
					$this->builder->field($filter->getField())->in($filter->getValue());
					break;
				
				case Filter::RANGE:
					$this->builder->field($filter->getField())->gte($filter->getFrom())->lte($filter->getTo());
					break;
			}
		}
		else
		{
			switch($filter->getOperator())
			{
				case Filter::LIKE:
					$this->builder->field($filter->getField())->equals(new \MongoRegex('/.*'.$filter->getValue().'.*/i'));
					break;

				case Filter::EQUAL:
					$this->builder->field($filter->getField())->equals($filter->getValue());
					break;

				case Filter::REFERENCES:
					//$this->builder->field($filter->getField())->ref
					break;
			}
		}
	}
	
	public function applySorting(array $sorting = null)
	{
		if($sorting !== null)
		{
			foreach($sorting as $sort)
			{
				$this->applySort($sort);
			}
		}
		return $this;
	}
	
	private function applySort(array $sort)
	{
		$column = key($sort);
		$order = $this->sortingDirections[$sort[$column]];
		$this->builder->sort($column, $order);
	}
	
	public function limit($offset, $limit)
	{
		if($offset < 0)
		{
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
		return $this->builder->getQuery()->count(true);
	}
	
	public function getRecordsByIds($ids)
	{
		return $this->builder->field('id')->in($ids)->getQuery()->execute();
	}
}