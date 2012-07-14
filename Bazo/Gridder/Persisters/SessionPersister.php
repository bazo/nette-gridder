<?php
namespace Gridder\Persisters;
/**
 * Description of SessionPersister
 *
 * @author Martin
 */
class SessionPersister extends BasePersister
{
    private 
	/** @var \Nette\Http\SessionSection */
	$storage
    ;


    public function __construct(\Nette\Http\SessionSection $storageSection)
    {
	$this->storage = $storageSection;
    }
    
    public function save($key, $data)
    {
	$this->storage->offsetSet($key, $data);
    }
    
    public function get($key)
    {
	return $this->storage->offsetGet($key);
    }
    
    public function keyExists($key)
    {
	return $this->storage->offsetExists($key);
    }
    
    public function delete($key)
    {
	$this->storage->offsetUnset($key);
    }
    
    public function reset()
    {
	$this->storage->remove();
    }
}