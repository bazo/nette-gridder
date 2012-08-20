<?php

namespace Gridder;

/**
 * Description of FilterMapper
 *
 * @author Martin
 */
class FilterMapper
{
	private static
		$map = array(
			'text' => 'TextFilter',
			'array' => 'ArrayFilter',
			'daterange' => 'DateRangeFilter',
			'multiselect' => 'MultiSelectFilter',
			'daterange' => 'DateRangeFilter',
			'range' => 'RangeFilter'
		)
	;

	/**
	 *
	 * @param type $parent
	 * @param type $type
	 * @return IFilter
	 */
	public static function map(Columns\BaseColumn $parent, $type)
	{
		$filterClass = 'Gridder\Filters\\' . self::$map[$type];
		return new $filterClass($parent, 'filter');
	}

}