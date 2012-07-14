<?php
namespace Gridder\Filters;
use Jobzine\Forms\Controls\MultipleDateField;
/**
 * Description of TextFilter
 *
 * @author Martin
 */
class DateRangeFilter extends Filter
{
    public function render()
    {
        return $this->name;
    }

    public function getFormControl($label)
    {
        $input = new MultipleDateField($label);
        $input->getControlPrototype()->class = 'text-filter';
        return $input;
    }

    public function getFilter(&$value)
    {
		$from = new \DateTime($value['from']);
		$to = new \DateTime($value['to']);
		$mongoValue = array(
			'from' => $from,
			'to' => $to,
		);
        return new DateRangeFilterObject($this->parent->name, self::RANGE, $mongoValue, $value['from'], $value['to']);
    }
}