<?php

namespace Gridder\Actions;

use Nette\ComponentModel\Component;
use Nette\Utils\Html;
use Nette\Utils\Strings;

/**
 * Description of DynamicActionColumn
 *
 * @author Martin
 */
class DynamicAction extends Action
{

	public $dynamicChange;


	public function render()
	{
		$this->fillParams();
		$output = '';
		$this->dynamicChange[0]($this->value, $this->record, $this);
		if ($this->showTitle == TRUE) {
			$title = $this->title;
		} else {
			$title = '';
		}
		if (empty($this->onActionRender)) {
			$icon = $this->icon != NULL ? $this->icon : $this->title;
			$output = Html::el('a')
					->add(Html::el('span')
							->class(sprintf('icon %s', Strings::lower($icon))))
					->href($this->presenter->link($this->destination, [$this->key => $this->value] + $this->params))->title($this->title)
					->add($title)
			;
		} else {
			foreach ($this->onActionRender as $function) {
				$output .= $function($this->value, $this->record, $this);
			}
		}
		if ($this->ajax)
			$output->addClass('ajax');
		return $output;
	}


}

