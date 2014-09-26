<?php

namespace Gridder;

use Gridder\Columns\BooleanColumn;
use Gridder\Columns\Column;
use Gridder\Columns\DateTimeColumn;
use Gridder\Columns\ObjectColumn;
use Gridder\Columns\TextColumn;
use Gridder\Columns\TimestampColumn;
use Gridder\Gridder;

/**
 * @author martin.bazik
 */
class ColumnMapper
{

	private static $types			 = [
		'string'	 => TextColumn::class,
		'object'	 => ObjectColumn::class,
		'boolean'	 => BooleanColumn::class,
		'datetime'	 => DateTimeColumn::class,
		'timestamp'	 => TimestampColumn::class
	];
	private static $defaultColumn	 = TextColumn::class;

	/**
	 *
	 * @param Gridder $grid
	 * @param string $name
	 * @param string $type
	 * @return Column
	 */
	public static function map(Gridder $grid, $name, $type)
	{
		if (isset(self::$types[$type])) {
			$class = self::$types[$type];
		} else {
			$class = self::$defaultColumn;
		}

		return new $class($grid, $name);
	}


}
