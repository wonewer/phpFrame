<?php
class RedisCache
{

    const DEFAULT_TIME = 3600;

    private $redis;

    public $key;

    public $content;

    public $time;
    
    private $refresh = false;
    
    public function __construct()
    {
        if(isset($_REQUEST['refreshRed'])&&$_REQUEST['refreshRed']){
            $this->refresh = true;
        }
    }

    public function getCache()
    {
        $content = '';
        ! is_numeric($this->time) && $this->time = self::DEFAULT_TIME;
        if (is_array($this->key)) {
            if (! isset($this->key[0]) || empty($this->key[0])) return $content;
            $this->redis = Yii::app()->redis->getRedis($this->key[0]);
            try {
            		$this->redis->ping();
            }catch (Exception $e){
                $this->redis = false;
            }
            switch (count($this->key)) {
                case 2:
                    if (! isset($this->key[1]) || empty($this->key[1])) return $content;
                    $content = $this->getItems($this->key);
                    break;
                case 1:
                    $content = $this->getGroup($this->key);
                    break;
            }
        } elseif (is_string($this->key)) {
            $this->redis = Yii::app()->redis->getRedis($this->key);
            try {
            	$this->redis->ping();
            }catch (Exception $e){
                $this->redis = false;
            }
            $content = $this->getNormal($this->key);
        }
        $objArr = (array)$this->redis;
        if(empty($objArr)){
            $this->redis = false;
        }
        return $content;
    }

    private function getNormal()
    {
        $content = '';
        if (!$this->redis || !$this->redis->ping() || ! ($this->redis->exists($this->key)) || $this->refresh) {
            $content = $this->content;
            $content = $content();
            if($this->redis) $this->redis->set($this->key, $content, $this->time);
        }else{
            $content = $this->redis->get($this->key);
        }
        return $content;
    }

    private function getItems()
    {
        $pKey = $this->key[0];
        $cKey = $this->key[1];
        $content = '';
        
        if(!$this->redis || !$this->redis->ping()){
        	$content = $this->content;
        	$content = $content();
        	return $content;
        }
        
        if (is_array($cKey)) {
            $content = $this->redis->hMget($pKey, $cKey);
        } else {
            if(!$this->redis->hExists($pKey, $cKey) || $this->refresh){
                $content = $this->content;
                $content = $content();
                $this->redis->hSet($pKey, $cKey, $content);
                $this->redis->expire ($pKey,$this->time);
            }else{
                $content = $this->redis->hGet($pKey, $cKey);
            }
        }
        
        return $content;
    }

    private function getGroup()
    {
        if(!$this->redis->ping()) return '';
        $pKey = $this->key[0];
        return $this->redis->hGetAll($pKey);
    }
}