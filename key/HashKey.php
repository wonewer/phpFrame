<?php

class HashKey extends KeyFactory
{

    public $pNode;

    public $cNode;

    public function __construct($pNode,$cNode)
    {
        $this->pNode = $pNode;
    	$this->cNode = $cNode;
    }
    
    public function getKey()
    {
        $key = array();
        ! empty($this->pNode) && $key[] = $this->pNode;
        ! empty($this->cNode) && $key[] = $this->cNode;
        return $key;
    }
}