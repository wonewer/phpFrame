<?php

/**
 *
 * @author yuanliangchen
 *        
 */
class MemcacheCache
{

    const DEFAULT_TIME = 3600;

    private $redis;

    public $key;

    public $content;

    public $time;
    
    private $refresh = false;

    public function __construct()
    {
    	if(isset($_REQUEST['refreshMem'])&&$_REQUEST['refreshMem']){
    		$this->refresh = true;
    	}
    }

    public function getCache()
    {
        $content = '';
        
        ! is_numeric($this->time) && $this->time = self::DEFAULT_TIME;
        
        $content = $this->getNormal();
        
        return $content;
    }

    public function getNormal()
    {
        $content = '';
        if (empty(Yii::app()->memcache) || ! (Yii::app()->memcache->offsetExists($this->key)) || $this->refresh ) {
            $content = $this->content;
            $content = $content();
            Yii::app()->memcache->set($this->key, $content, $this->time);
        }else{
            $content = Yii::app()->memcache->get($this->key);
        }
        return $content;
    }
}
?>