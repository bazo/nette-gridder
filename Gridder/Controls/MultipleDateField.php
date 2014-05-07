<?php

namespace Gridder\Controls;

use DateTime;
use Nette\Diagnostics\Debugger;
use Nette\Forms\Controls\TextBase;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Arrays;
use Nette\Utils\Html;



/**
 * @author Martin Bažík
 */
class MultipleDateField extends TextInput
{

	/**
	 * Generates control's HTML element.
	 * @return Html
	 */
	public function getControl()
	{
		$fromControl = Html::el('input')->class('datepicker')->autocomplete('on');
		$fromControl->name = $this->getHtmlName() . '[from]';
		$fromControl->disabled = $this->disabled;
		$fromControl->id = $this->getHtmlId() . '-from';
		if (isset($this->value['from'])) {
			$from = $this->value['from'];

			if ($from instanceof DateTime) {
				$fromControl->value($this->value['from']->format('d.m.Y'));
			} else {
				$fromControl->value($this->value['from']);
			}
		}

		$toControl = Html::el('input')->class('datepicker')->autocomplete('on');
		$toControl->name = $this->getHtmlName() . '[to]';
		$toControl->disabled = $this->disabled;
		$toControl->id = $this->getHtmlId() . '-to';
		if (isset($this->value['to'])) {
			$to = $this->value['to'];
			if ($to instanceof DateTime) {
				$toControl->value($this->value['to']->format('d.m.Y'));
			} else {
				$toControl->value($this->value['to']);
			}
		}

		$control = Html::el('span')->add($fromControl)->add($toControl)->class('range-input');
		$control->disabled = $this->disabled;
		return $control;
	}


	/**
	 * Sets control's value.
	 * @param  string
	 * @return TextBase  provides a fluent interface
	 */
	public function setValue($value)
	{
		$this->value = \is_array($value) ? $value : array('from' => null, 'to' => null);
		return $this;
	}


	/**
	 * Returns control's value.
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Loads HTTP data.
	 * @return void
	 */
	public function loadHttpData()
	{
		$path = \explode('[', \strtr(\str_replace(array('[]', ']'), '', $this->getHtmlName()), '.', '_'));

		$origValue = Arrays::get($this->getForm()->getHttpData(), $path);

		$from = isset($origValue['from']) ? $origValue['from'] : '';
		$to = isset($origValue['to']) ? $origValue['to'] : '';
		$value = array(
			'from' => $from,
			'to' => $to
		);
		$this->setValue($value);
	}


}
