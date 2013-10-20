<?php

namespace Gridder\Columns;

/**
 * TextColumn
 *
 * @author martin.bazik
 */
class BooleanColumn extends BaseColumn
{

	public $TRUEValue = 'áno';
	public $FALSEValue = 'nie';


	protected function formatValue($value)
	{
		return $value === true ? $this->trueValue : $this->falseValue;
	}


}

