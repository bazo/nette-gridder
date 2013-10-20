<?php

namespace Gridder\Actions;

use Nette\ComponentModel\Component;
use Nette\Utils\Html;
use Nette\Utils\Strings;

/**
 * Description of DibiDatagridAction
 *
 * @author Martin
 */
class Action extends Component
{

	protected $title;
	protected $destination;
	protected $key = 'id';
	protected $value;
	protected $record;
	protected $showTitle = TRUE;
	protected $ajax;
	protected $icon;
	protected $params = [];
	protected $dynamicParams = [];
	protected $presenter;
	protected $hasIcon = FALSE;
	public $onActionRender = [];


	public function setPresenter(&$presenter)
	{
		$this->presenter = $presenter;
	}


	/**
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 *
	 * @param string $title
	 * @return Action
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}


	/**
	 *
	 * @return string
	 */
	public function getDestination()
	{
		return $this->destination;
	}


	/**
	 *
	 * @param string $destination
	 * @return Action
	 */
	public function setDestination($destination)
	{
		$this->destination = $destination;
		return $this;
	}


	/**
	 *
	 * @return string Action key field name 
	 */
	public function getKey()
	{
		return $this->key;
	}


	/**
	 *
	 * @param string $key
	 * @return Action
	 */
	public function setKey($key)
	{
		$this->key = $key;
		return $this;
	}


	/**
	 *
	 * @return Action
	 */
	public function showTitle()
	{
		$this->showTitle = true;
		return $this;
	}


	/**
	 *
	 * @return Action 
	 */
	public function hideTitle()
	{
		$this->showTitle = false;
		return $this;
	}


	/**
	 *
	 * @param bool $value
	 * @return Action
	 */
	public function setAjax($value)
	{
		$this->ajax = $value;
		return $this;
	}


	protected function fillParams()
	{
		foreach ($this->dynamicParams as $param => $field) {
			if (isset($this->record->$field))
				@$this->params[$param] = $this->record->$field;
		}
	}


	/**
	 *
	 * @param mixed $record
	 * @return Action
	 */
	public function setRecord($record)
	{
		$key = $this->key;
		$this->value = $record->$key;
		$this->record = $record;
		return $this;
	}


	/**
	 * Returns shown title
	 * @return string
	 */
	public function getShowTitle()
	{
		return $this->showTitle;
	}


	/**
	 * Sets the title that will be shown on action
	 * @param string $showTitle
	 * @return Action
	 */
	public function setShowTitle($showTitle)
	{
		$this->showTitle = $showTitle;
		return $this;
	}


	/**
	 * Return the name of icon
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	 * Sets icon
	 * @param string $icon
	 * @return Action
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
		$this->hasIcon = true;
		return $this;
	}


	/**
	 * Adds dynamic parameter
	 * @param string $paramName
	 * @param string $field
	 * @return Action
	 */
	public function addParam($paramName, $field = null)
	{
		$field = $field == null ? $paramName : $field;
		$this->dynamicParams[$paramName] = $field;
		return $this;
	}


	/**
	 * Adds static variable
	 * @param string $variable name
	 * @param string $value value
	 * @return Action
	 */
	public function addVariable($variable, $value = null)
	{
		@$this->params[$variable] = $value;
		return $this;
	}


	public function render()
	{
		$this->fillParams();
		$output = '';
		if ($this->showTitle == true) {
			$title = $this->title;
		} else {
			$title = '';
		}
		$icon = $this->icon != null ? $this->icon : $this->title;
		$output = Html::el('a');
		if ($this->hasIcon) {
			$output->add(Html::el('span')
							->class(sprintf('icon %s', Strings::lower($icon))));
		}

		$output->href($this->presenter->link($this->destination, [$this->key => $this->value] + $this->params))
				->title($this->title)
				->add($title)
		;
		if (!empty($this->onActionRender)) {
			foreach ($this->onActionRender as $function) {
				$output = $function($this->value, $this->record, $this->title, $output);
			}
		}
		if ($output instanceof Html) {
			if ($this->ajax) {
				$output->addClass('ajax');
			}
		}
		return $output;
	}


}

