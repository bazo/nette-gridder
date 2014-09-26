<?php

namespace Gridder\Columns;


/**
 * DateTimeColumn
 *
 * @author martin.bazik
 */
class TimestampColumn extends BaseColumn
{

	private $format = 'd.m.Y';

	public function getFormat()
	{
		return $this->format;
	}


	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}


	protected function formatValue($value)
	{
		if (empty($value)) {
			return 'N/A';
		}
		$dt = new \DateTimeImmutable('@' . $value);

		return $dt->format($this->format);
	}


}
