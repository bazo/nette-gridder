<?php

namespace Gridder\Filters;

use Bazo\Forms\Controls\MultipleField;

/**
 * Description of TextFilter
 *
 * @author Martin
 */
class RangeFilter extends Filter
{

	public function render()
	{
		return $this->name;
	}


	public function getFormControl($label)
	{
		$input = new MultipleField($label);
		$input->getControlPrototype()->class = 'text-filter';
		return $input;
	}


	public function getFilter(&$value)
	{
		return new RangeFilterObject($this->parent->name, self::RANGE, $value, $value['from'], $value['to']);
	}


}

