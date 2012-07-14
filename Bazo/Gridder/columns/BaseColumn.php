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
	protected
		$caption,
		$record,
		$value,
		$hasFilter,
		$sortable = false
	;
	
	public
		$onCellRender = array(),
		$onHeaderRender = array(),
		$valueModifier = array()
	;
	
	public function getCaption()
	{
		if($this->caption !== null)
		{
			return $this->caption;
		}
		else
		{
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
		$this->record = $record;
		$this->value = $record[$this->name];
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
        $this->hasFilter = true;
        $this->parent->hasFilters = true;
        return FilterMapper::map($this, $type);
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
		foreach($this->valueModifier as $modifier)
		{
			$value = $modifier($value, $this->record);
		}
		return $value;
	}
	
	public function setSortable($sortable = true)
	{
		$this->sortable = $sortable;
		return $this;
	}

	public function isSortable()
	{
		return $this->sortable;
	}


}