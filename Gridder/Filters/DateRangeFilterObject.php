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

	protected $from;
	protected $to;


	public function __construct($field, $operator, $value, $from, $to, $originalFieldName)
	{
		$this->field = $field;
		$this->operator = $operator;
		$this->value = $value;
		$this->from = $from;
		$this->to = $to;
		$this->originalFieldName = $originalFieldName;
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
		return $this->value['to'];
	}


	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}


	public function notEmpty()
	{
		return $this->from !== '' and $this->to !== '';
	}


}

