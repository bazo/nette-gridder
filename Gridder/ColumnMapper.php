<?php

namespace Gridder;

/**
 * ColumnMapper
 *
 * @author martin.bazik
 */
class ColumnMapper
{

	private static $types = [
		'string' => 'TextColumn',
		'object' => 'ObjectColumn',
		'boolean' => 'BooleanColumn',
		'datetime' => 'DateTimeColumn'
	];
	private static $defaultColumn = 'TextColumn';


	/**
	 *
	 * @param Grid $grid
	 * @param type $name
	 * @param type $type
	 * @return Columns\Column 
	 */
	public static function map(Gridder $grid, $name, $type)
	{
		$namespace = '\Gridder\Columns\\';
		
		if (isset(self::$types[$type])) {
			$class = self::$types[$type];
		} else {
			$class = self::$defaultColumn;
		}
		
		$class = $namespace . $class;
		return new $class($grid, $name);
	}


}

