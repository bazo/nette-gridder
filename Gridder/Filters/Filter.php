<?php

namespace Gridder\Filters;

use \Nette\Application\UI\Control;
use Nette\Forms\Controls\TextInput;

/**
 * BaseFilter
 *
 * @author Martin Bažík
 * @package Core
 */
abstract class Filter extends Control implements IFilter
{

	protected $operator = 'like';
	protected $title;


	const LIKE = 'like';
	const EQUAL = '=';
	const REFERENCES = 'ref';
	const IN = 'in';
	const RANGE = 'range';


	public function setOperator($operator)
	{
		$this->operator = $operator;
	}


	public function render()
	{
		return $this->name;
	}


	public function getFormControl($label)
	{
		return new TextInput($label);
	}


	public function apply(&$dql, $value)
	{
		$dql->where($this->name . ' like %?%', $value);
	}


	public function getFilter(&$value)
	{
		return new FilterObject($this->parent->name, $this->operator, $value, '');
	}


}

