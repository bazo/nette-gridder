<?php

namespace Gridder\Columns;


use Nette\Application\UI\Control;
use Gridder\FilterMapper;

/**
 * BaseColumn
 *
 * @author martin.bazik
 */
class BaseColumn extends Control implements Column
{

	protected $caption;
	protected $record;
	protected $value;
	protected $hasFilter;
	protected $sortable		 = FALSE;
	protected $columnPrefix;
	public $onCellRender	 = [];
	public $onHeaderRender	 = [];
	public $valueModifier	 = [];

	public function getCaption()
	{
		if ($this->caption !== NULL) {
			return $this->caption;
		} else {
			return $this->name;
		}
	}


	/**
	 *
	 * @param type $caption
	 * @return \Grid\Columns\BaseColumn
	 */
	public function setCaption($caption)
	{
		$this->caption = $caption;
		return $this;
	}


	public function renderHeader()
	{
		return $this->getCaption();
	}


	/**
	 *
	 * @param type $record
	 * @return \Grid\Columns\BaseColumn
	 */
	public function setRecord($record)
	{
		$this->record	 = $record;
		$this->value	 = isset($record[$this->name]) ? $record[$this->name] : NULL;
		return $this;
	}


	protected function formatValue($value)
	{
		return $value;
	}


	/**
	 *
	 * @param string $type
	 * @return IFilter
	 */
	public function setFilter($type)
	{
		$this->hasFilter = TRUE;
		$this->parent->setHasFilters();
		return FilterMapper::map($this, $type);
	}


	public function addTextFilter($fieldName = NULL)
	{
		return $this->setFilter(\Gridder\Filters\Filter::TEXT)->setOriginalFieldName($fieldName);
	}


	public function addArrayFilter()
	{
		return $this->setFilter(\Gridder\Filters\Filter::ARRAY_FILTER);
	}


	public function addDateRangeFilter()
	{
		return $this->setFilter(\Gridder\Filters\Filter::DATE_RANGE);
	}


	public function addMultiselectFilter()
	{
		return $this->setFilter(\Gridder\Filters\Filter::MULTISELECT);
	}


	public function addRangeFilter()
	{
		return $this->setFilter(\Gridder\Filters\Filter::RANGE);
	}


	public function hasFilter()
	{
		return $this->hasFilter;
	}


	public function getFilter()
	{
		return $this->getComponent('filter')->getFormControl($this->getCaption());
	}


	public function render()
	{
		$value = $this->formatValue($this->value);
		foreach ($this->valueModifier as $modifier) {
			$value = $modifier($value, $this->record);
		}
		return $value;
	}


	public function setSortable($sortable = TRUE)
	{
		$this->sortable = $sortable;
		return $this;
	}


	public function enableSort($rootEntity = 'n')
	{
		$this->sortable		 = TRUE;
		$this->columnPrefix	 = $rootEntity;
		return $this;
	}


	function getColumnPrefix()
	{
		return $this->columnPrefix;
	}


	public function isSortable()
	{
		return $this->sortable;
	}


}
