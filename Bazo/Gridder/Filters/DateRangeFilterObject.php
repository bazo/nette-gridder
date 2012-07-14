<?php
namespace Gridder\Filters;
/**
 * FilterObject
 *
 * @author Martin Bažík
 * @package Core
 */
class DateRangeFilterObject extends FilterObject
{
    protected
		$from,
		$to
    ;

    public function  __construct($field, $operator, $value, $from, $to)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
		$this->from = $from;
		$this->to = $to;
    }

	public function getFrom()
	{
		return $this->value['from'];
	}

	public function setFrom($from)
	{
		$this->from = $from;
		return $this;
	}

	public function getTo()
	{
		//var_dump($this->value['to']);exit;
		return $this->value['to'];
	}

	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}
	
	public function notEmpty()
	{
		//var_dump($this->value, $this->emptyValue, $this->to, $this->from);exit;
		return $this->from !== '' and $this->to !== '';
	}
}