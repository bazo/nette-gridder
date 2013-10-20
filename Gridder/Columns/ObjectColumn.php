<?php

namespace Gridder\Columns;

/**
 * ObjectColumn
 *
 * @author martin.bazik
 */
class ObjectColumn extends BaseColumn
{

	protected function formatValue($value)
	{
		return $value;
	}


}

