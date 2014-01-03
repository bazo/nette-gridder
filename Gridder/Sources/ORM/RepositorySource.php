<?php

namespace Gridder\Sources\ORM;

use Doctrine\ORM\EntityRepository;
use \Doctrine\ORM\QueryBuilder;



/**
 * Description of EntitySource
 *
 * @author Martin
 */
class RepositorySource extends QueryBuilderSource
{

	protected $supportSFiltering = TRUE;
	protected $supportsSorting = TRUE;



	public function __construct(EntityRepository $repository, $hydrationMode = self::HYDRATION_SIMPLE, $alias = 'entity')
	{
		$queryBuilder = $repository->createQueryBuilder($alias);
		parent::__construct($queryBuilder, $hydrationMode);
	}


	public function applyFilters($filters)
	{
		if ($filters == null) {
			return $this;
		}

		foreach ($filters as $columnName => $filter) {
			$filter instanceof \Gridder\Filters\FilterObject;
			if (!$filter->isEmpty()) {
				$where = $this->builder->getRootAlias() . '.' . $columnName . ' ' . $filter->getOperator() . ' :' . $filter->getField();
				if ($this->filterChainMode == self::CHAIN_MODE_AND) {
					$this->builder->andWhere($where);
				}
				if ($this->filterChainMode == self::CHAIN_MODE_OR) {
					$this->builder->orWhere($where);
				}
				$this->builder->setParameter($filter->getField(), $filter->getQueryValue());
			}
		}
		return $this;
	}


	public function supportsFiltering()
	{
		return true;
	}


	public function supportsSorting()
	{
		return true;
	}


}
