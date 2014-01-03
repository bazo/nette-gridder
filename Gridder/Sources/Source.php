<?php

namespace Gridder\Sources;

/**
 *
 * @author martin.bazik
 */
interface Source
{
	public function applyFilters($filters);
	public function applySorting(array $sorting = NULL);
}

