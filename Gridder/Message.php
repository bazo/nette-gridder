<?php

namespace Gridder;

/**
 * Message
 *
 * @author Martin
 */
class Message
{

	/** @var string */
	private $type;

	/** @var string */
	private $message;


	public function __construct($message, $type)
	{
		$this->message = $message;
		$this->type = $type;
	}


	public function getType()
	{
		return $this->type;
	}


	public function getMessage()
	{
		return $this->message;
	}


}

