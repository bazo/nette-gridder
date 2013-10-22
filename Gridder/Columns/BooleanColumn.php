<?php

namespace Gridder\Columns;

/**
 * TextColumn
 *
 * @author martin.bazik
 */
class BooleanColumn extends BaseColumn
{

	public $trueValue = 'yes';
	public $falseValue = 'no';


	protected function formatValue($value)
	{
		return $value === true ? $this->trueValue : $this->falseValue;
	}


}

