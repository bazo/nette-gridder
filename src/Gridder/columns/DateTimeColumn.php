<?php
namespace Gridder\Columns;
/**
 * TextColumn
 *
 * @author martin.bazik
 */
class DateTimeColumn extends BaseColumn
{
	private
		$format = 'd.m.Y'
	;

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
		if($value === null)
		{
			return 'N/A';
		}
		return $value->format($this->format);
	}
}