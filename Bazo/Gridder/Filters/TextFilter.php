<?php
namespace Gridder\Filters;
use Nette\Forms\Controls\TextInput;
/**
 * Description of TextFilter
 *
 * @author Martin
 */
class TextFilter extends Filter
{
    public function render()
    {
        return $this->name;
    }

    public function getFormControl($label)
    {
        $input = new TextInput($label);
        $input->getControlPrototype()->class = 'text-filter';
        return $input;
    }

    public function getFilter(&$value)
    {
        return new FilterObject($this->parent->name, Filter::LIKE, $value);
    }
}