<?php
Yii::import('search.components.cache.type.*');
class Cache
{

    public $cache;

    static function instance()
    {
        return new Cache();
    }

    public function getCache($type)
    {
        $this->cache = $type;
        return $this;
    }

    /**
     * 缓存处理
     * redis:支持hash缓存，使用hashKey获取Key，现支持单个缓存，可通过配置cNode获取聚合信息
     *
     * @param string $key            
     * @param string $action            
     * @param string $time            
     * @return string unknown
     */
    public function optCache($key = false, $action = false, $time = false)
    {
        $content = '';
        if (! empty($action) && ! ($action instanceof Closure)) {
            return $content;
        }
        
        switch ($this->cache) {
            case 'redis':
                $redis = new RedisCache();
                $redis->key = $key;
                $action && $redis->content = $action;
                $time && $redis->time = $time;
                $content = $redis->getCache();
                break;
            case 'memcache':
                $memcache = new MemcacheCache();
                $memcache->key = $key;
                $memcache->content = $action;
                $time && $memcache->time = $time;
                $content = $memcache->getCache();
                break;
            default:
                return $content;
        }
        
        return $content;
    }
}