<?php
/*
 * @User: EricGU178
 * @LastEditors: EricGU178
 * @Date: 2019-03-30 16:57:54
 * @LastEditTime: 2019-07-22 14:37:04
 */
namespace tool;

/**
 * redis 工具类
 *
 * @author EricGU178
 */
class Redis extends Base
{
    /**
     * redis 配置
     *
     * @var array
     */
    static protected $config   =   [
        "host"  =>  '127.0.0.1',
        "port"  =>  '6379',
    ];

    /**
     * redis 实例
     *
     * @var void
     */
    protected $redis;

    /**
     * 初始化
     *
     * @param array $config
     * @return self
     * @author EricGU178
     */
    static public function init($config=[])
    {
        if (!empty($config)) {
            self::$config   =   $config;
        }
        return new self(self::$config);
    }

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect(
            self::$config['host'],
            self::$config['port'],
            isset(self::$config['exp']) ? self::$config['exp'] : 10
        );

        if (isset(self::$config['password'])) {
            $this->redis->auth(self::$config['password']);
        }
    }

    /**
     * 设置key-value
     *
     * @param $key
     * @param $value
     * @return void
     * @author EricGU178
     */
    public function keySet($key,$value)
    {
        if (is_object($value)||is_array($value)) {
            $value = serialize($value);
        }
        if (!$this->redis->set($key,$value)) {
            throw new \Exception("设置{$key}错误");
        }
        if (!$this->keyExpire($key)) {
            throw new \Exception("设置过期时间{$key}错误");
        }
    }

    /**
     * 获取key-value value
     *
     * @param $key
     * @return void
     * @author EricGU178
     */
    public function keyGet($key)
    {
        $value = $this->redis->get($key);
        $value_serl = @unserialize($value);
        if(is_object($value_serl)||is_array($value_serl)){
            return $value_serl;
        }
        return $value;
    }

    /**
     * 检查key存在？
     *
     * @param [type] $key
     * @return void
     * @author EricGU178
     */
    public function keyExists($key)
    {
        if ($this->redis->exists($key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置过期时间
     *
     * @param string $key
     * @param string $expire 默认 1天 86400 秒
     * @return void
     * @author EricGU178
     */
    public function keyExpire($key,$expire=86400)
    {
        return $this->redis->expire($key,$expire);
    }

    public function keyDel($key)
    {
        return $this->redis->del($key);
    }

    /**
     * 获取原生redis
     *
     * @return void
     * @author EricGU178
     */
    public function getRedis()
    {
        return $this->redis;
    }

    //////////////////////////////////////
    // list 类型
    //////////////////////////////////////

    /**
     * list 插入
     *
     * @param string $key
     * @param string $value
     * @return void
     * @author EricGU178
     */
    public function lPush($key,$value)
    {
        return $this->redis->lpush($key,$value);
    }

    /**
     * 移出并获取列表的第一个元素， 如果列表没有元素会阻塞列表直到等待超时或发现可弹出元素为止。
     * 
     * @param string $key
     * @param integer $timeout
     * @return void
     * @author EricGU178
     */
    public function blPop($key,$timeout=600)
    {
        return $this->redis->blpop($key,$timeout);
    }

    /**
     * 命令用于移除并返回列表的第一个元素。
     * 
     * 列表的第一个元素。 当列表 key 不存在时，返回 nil 。
     * @param [type] $key
     * @return void
     * @author EricGU178
     */
    public function lPop($key)
    {
        return $this->redis->lpop($key);
    }

    /**
     * 获取列表长度
     *
     * @param string $key
     * @return void
     * @author EricGU178
     */
    public function lLen($key)
    {
        return $this->redis->llen($key);
    }

    //////////////////////////////////////
    // hash 类型
    //////////////////////////////////////

    /**
     * Redis Hmset 命令用于同时将多个 field-value (字段-值)对设置到哈希表中。
     * 此命令会覆盖哈希表中已存在的字段。
     * 如果哈希表不存在，会创建一个空哈希表，并执行 HMSET 操作。
     *
     * @param string $key
     * @param array $array
     * @return void
     * @author EricGU178
     */
    public function hMSet($key,$array)
    {
        return $this->redis->hMSet($key,$array);
    }

    /**
     * 删除一个或多个哈希表字段
     *
     * @param string $key
     * @param array|string $field
     * @return void
     * @author EricGU178
     */
    public function hDel($key,$field)
    {
        $num = 0;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $value) {
            if ($this->redis->hDel($key,$value)) {
                $num ++;
            }
        }
        return $num;
    }

    /**
     * 返回给定字段的值。如果给定的字段或 key 不存在时，返回 nil 。
     *
     * @param string $key
     * @param string $field
     * @return void
     * @author EricGU178
     */
    public function hGet($key,$field)
    {
        return $this->redis->hGet($key,$field);
    }
    
    /**
     * 返回键
     *
     * @param [type] $key
     * @return void
     * @author EricGU178
     */
    public function keys($key)
    {
        return $this->redis->keys($key);
    }
}