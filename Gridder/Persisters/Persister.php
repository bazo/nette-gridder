<?php
namespace Gridder\Persisters;
/**
 *
 * @author Martin
 */
interface Persister
{
    public function save($key, $data);
    public function get($key);
    public function keyExists($key);
    public function delete($key);
}