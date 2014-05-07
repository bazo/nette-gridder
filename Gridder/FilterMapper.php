<?php

namespace Gridder;

use Gridder\Filters\Filter;



/**
 * FilterMapper
 *
 * @author Martin
 */
class FilterMapper
{

	private static $map = [
		Filter::TEXT => 'TextFilter',
		Filter::ARRAY_FILTER => 'ArrayFilter',
		Filter::DATE_RANGE => 'DateRangeFilter',
		Filter::MULTISELECT => 'MultiSelectFilter',
		Filter::RANGE => 'RangeFilter'
	];



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
