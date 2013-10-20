<?php

namespace Gridder\Filters;

/**
 * FilterObject
 *
 * @author Martin Bažík
 * @package Core
 */
class RangeFilterObject extends FilterObject
{

	protected $from;
	protected $to;
	protected $emptyValue = [
		'from' => '',
		'to' => ''
	];


	public function __construct($field, $operator, $value, $from, $to)
	{
		$this->field = $field;
		$this->operator = $operator;
		$this->value = $value;
		$this->from = $from;
		$this->to = $to;
	}


	public function getFrom()
	{
		return (int) $this->from;
	}


	public function setFrom($from)
	{
		$this->from = $from;
		return $this;
	}


	public function getTo()
	{
		return (int) $this->to;
	}


	public function setTo($to)
	{
		$this->to = $to;
		return $this;
	}


	public function notEmpty()
	{
		return $this->value !== $this->emptyValue;
	}


}

