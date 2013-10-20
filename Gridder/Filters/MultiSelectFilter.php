<?php

namespace Gridder\Filters;

use Nette\ComponentModel\IContainer;

/**
 * ArrayFilter
 *
 * @author Martin Bažík
 */
class MultiSelectFilter extends Filter
{

	private $filterField;
	private $items;


	public function __construct(IContainer $parent, $name, $items = [], $filterField = NULL)
	{
		parent::__construct($parent, $name);
		$this->items = $items;
		if ($filterField === NULL) {
			$this->filterField = $parent->name;
		} else {
			$this->filterField = $filterField;
		}
	}


	public function setItems($items)
	{
		$this->items = $items;
		return $this;
	}


	public function getFormControl($label)
	{
		return new \Jobzine\Forms\Controls\CheckboxList($label, $this->items);
	}


	public function getFilter(&$value)
	{
		return new FilterObject($this->filterField, self::IN, $value, NULL);
	}


	public function getFilterField()
	{
		return $this->filterField;
	}


	public function setFilterField($filterField)
	{
		$this->filterField = $filterField;
		return $this;
	}


}

