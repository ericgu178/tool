<?php
namespace tool;

/**
 * 签名类
 *
 * @author EricGU178
 */
class Sign extends Base
{
    /**
     * 签名 md5
     *
     * @param array $data
     * @param string $key
     * @param bool $capital true
     * @return string
     * @author EricGU178
     */
    static public function sign_md5(array $data, string $key , bool $capital = true):string
    {
        if (count($data) == 0) \trigger_error('签名的数据不能为空');
        \ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            $str .= "{$key}={$value}&";
        }
        $str .= 'key=' . $key;
        if ($capital) {
            return strtoupper(md5($str));
        } else {
            return strtolower(md5($str));
        }
    }

    /**
     * 验证签名 md5
     *
     * @param array $data
     * @param string $key
     * @param boolean $capital
     * @return boolean
     * @author EricGU178
     */
    static public function check_sign_md5(array $data, string $key , bool $capital = true):bool
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            $str .= "{$key}={$value}&";
        }
        $str .= 'key=' . $key;
        if ($capital) {
            $pre_sign = strtoupper(md5($str));
            return $sign === $pre_sign ? true : false;
        } else {
            $pre_sign = strtolower(md5($str));
            return $sign === $pre_sign ? true : false;
        }
    }

    /**
     * 公钥格式化
     *
     * @param string $pubKey
     * @return void
     * @author EricGU178
     */
    static public function formatPubKey($pubKey)
    {
        $fKey = "-----BEGIN PUBLIC KEY-----\n";
        $len = strlen($pubKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($pubKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END PUBLIC KEY-----";
        return $fKey;
    }

    /**
     * 私钥格式化
     *
     * @param string $priKey
     * @return void
     * @author EricGU178
     */
    static public function formatPriKey($priKey) 
    {
        $fKey = "-----BEGIN RSA PRIVATE KEY-----\n";
        $len = strlen($priKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($priKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END RSA PRIVATE KEY-----";
        return $fKey;
    }


    /**
     * 检查方法 检查参数
     *
     * @param array $params
     * @param array $checkParams
     * @param string $methods
     * @return void
     * @author EricGU178
     */
    static public function check($params,$checkParams,$method)
    {
        try {
            if (!self::checkRequestMethod($method)) throw new \Exception('请求方法错误');
            $e = self::checkParams($params,$checkParams);
            if (!is_null($e)) throw new \Exception($e);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 检测必传参数
     *
     * @param array $params 所有入参
     * @param array $checkParams 需要检查必传的参数
     * @return void
     * @author EricGU178
     */
    static public function checkParams($params,$checkParams)
    {
        try {
            foreach ($checkParams as $key) {
                if (!array_key_exists($key, $params)) {
                    throw new \Exception('缺少必传字段请检查' . '[' . $key . ']');
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * 检测请求方式
     *
     * @param method 方法
     * @return void
     * @author EricGU178
     */
    static public function checkRequestMethod($method = 'get'):bool
    {
        $method = strtoupper($method);
        return $_SERVER['REQUEST_METHOD'] !== $method ? false : true;
    }
}