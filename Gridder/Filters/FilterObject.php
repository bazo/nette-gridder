<?php

namespace Gridder\Filters;

/**
 * FilterObject
 *
 * @author Martin Bažík
 * @package Core
 */
class FilterObject
{

	protected $field;
	protected $operator;
	protected $value;
	protected $emptyValue = '';
	protected $originalFieldName;



	public function __construct($field, $operator, $value, $emptyValue = '', $originalFieldName = NULL)
	{
		$this->field = $field;
		$this->operator = $operator;
		$this->value = $value;
		$this->emptyValue = $emptyValue;
		$this->originalFieldName = $originalFieldName;
	}


	public function getValue()
	{
		return $this->value;
	}


	public function getEmptyValue()
	{
		return $this->emptyValue;
	}


	public function getOperator()
	{
		return $this->operator;
	}


	public function getQueryValue()
	{
		return $this->queryValue;
	}


	public function getField()
	{
		return $this->field;
	}


	public function getFilterFieldName()
	{
		return isset($this->originalFieldName) ? $this->originalFieldName : $this->field;
	}


	public function isEmpty()
	{
		$empty = FALSE;
		if (is_null($this->value) or $this->value === $this->emptyValue) {
			$empty = TRUE;
		}

		return $empty;
	}


	public function notEmpty()
	{
		return !$this->isEmpty();
	}


}
