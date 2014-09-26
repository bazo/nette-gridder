<?php

namespace Gridder\Sources\Neo4j;


/**
 * @author Martin Bažík <martin@bazo.sk>
 */
class Neo4jRowIterator extends \Bazo\Neo4jTools\Neo4jRowIterator
{
	public function current()
	{
		$node = $this->resultSet->current()->current();
		$properties = array_merge(['id' => $node->getId()], $node->getProperties());
		return $properties;
	}
}
