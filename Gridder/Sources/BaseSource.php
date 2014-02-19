<?php

namespace Gridder\Sources;

/**
 * BaseSource
 *
 * @author martin.bazik
 */
class BaseSource implements Source
{

	protected $primaryKey;
	protected $filterChainMode = 'and';
	protected $supportSFiltering = FALSE;
	protected $supportsSorting = FALSE;
	protected $metadata;

	const CHAIN_MODE_AND = 'and';
	const CHAIN_MODE_OR = 'or';


	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
		return $this;
	}


	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}


	public function setFilterChainMode($filterChainMode)
	{
		$this->filterChainMode = $filterChainMode;
		return $this;
	}


	public function getRows()
	{

	}


	public function getTotalCount()
	{

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
	}


	public function supportsFiltering()
	{
		return $this->supportSFiltering;
	}


	public function supportsSorting()
	{
		return $this->supportsSorting;
	}


	public function applySorting(array $sorting = NULL)
	{

	}

	public function getMetadata()
	{
		return $this->metadata;
	}

}

