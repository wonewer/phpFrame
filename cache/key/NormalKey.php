<?php

class NormalKey extends KeyFactory
{

    private $key;
    
    public function __construct($key)
    {
    	$this->key = $key;
    }
    
    public function getKey()
    {
        return $this->key;
    }
}