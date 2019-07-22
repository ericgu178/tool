<?php
namespace tool;
/**
 * 随机数类
 *
 * @author EricGU178
 */
class Rand extends Base
{
    /**
     * 创建一个随机数
     *
     * @param string $title
     * @param integer $num
     * @return void
     */
    static public function createRandNum($num=8)
    {
        $rand = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $num);
        return $rand;
    }

    /**
     * 创建一个前缀和随机数
     *
     * @param string $title
     * @return void
     */
    static public function createPrefixAndRand($title='RAND') 
    {
        $rand = self::createRandNum(15);
        return $title . $rand;
    }

    /**
     * 创建一个随机字符串
     *
     * @param integer $length
     * @return string
     */
    static public function createRandStr(int $length = 8):string
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
        'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
        't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', 
        '@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_', 
        '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',', 
        '.', ';', ':', '/', '?', '|'];
        // 在 $chars 中随机取 $length 个数组元素键名
        $keys = array_rand($chars, $length); 
        $rand_str = '';
        for($i = 0; $i < $length; $i++) {
            $rand_str .= $chars[$keys[$i]];
        }
        return $rand_str;
    }

    /**
     * 生成一个随机电话号码
     *
     * @param integer $hide_start 开始隐藏的位数
     * @param integer $hide_length 模糊
     * @param string $hide_str 模糊串
     * @return string
     */
    static public function createTelephoneNumber(int $hide_start = 3 , int $hide_length = 4, string $hide_str = '*' , bool $isHide = false):string
    {
        $arr = [
            130,131,132,133,134,135,136,137,138,139,
            144,147,
            150,151,152,153,155,156,157,158,159,
            160,161,162,163,165,166,167,168,169,
            176,177,178,
            180,181,182,183,184,185,186,187,188,189,
        ];
        // 电话号
        $telephone = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
        if ($isHide) {
            $hs = $hide_str;
            for ($i = 0; $i < $hide_length - 1; $i++) { 
                $hide_str .= $hs;
            }
            $telephone =  str_replace(substr($telephone,$hide_start,$hide_length),$hide_str , $telephone);
        }
        return $telephone;
    }

    /**
     * 返回格式化日期
     *
     * @param string $format
     * @return string
     * @author EricGU178
     */
    static public function formatDate(string $format = 'YmdHis'):string
    {
        return date($format);
    }
}