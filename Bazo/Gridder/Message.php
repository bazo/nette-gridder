<?php
namespace Gridder;
/**
 * Description of Message
 *
 * @author Martin
 */
class Message
{
    private 
	$type,
	$message
    ;
    
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