<?php
namespace Gridder\Filters;
use Jobzine\Forms\Controls\MultipleField;
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
		/*
		$value = array(
			'from' => (int)$value['from'],
			'to' => (int)$value['to'],
		);
		 * 
		 */
        return new RangeFilterObject($this->parent->name, self::RANGE, $value, $value['from'], $value['to']);
    }
}