<?php
namespace tool;

use tool\Rand;
/**
 * 文件操作类
 *
 * @author EricGU178
 */
class File extends Base
{
    static protected $dir  =   null;   //  文件夹目录

    static protected $write_data = null; // 写入数据

    static public function dir($dir)
    {
        self::$dir = $dir;
        return self;
    }

    public function setData(string $data, $data_type = 0)
    {
        if ($data_type == 1) {
            $data = file_get_contents($data);
        }
        self::$write_data = $data;
        return self;
    }

    public function save($filename)
    {
        $date = Rand::formatDate('Ymd');
        $file_path = self::$dir . '/' . $date . '/' . $filename;
        $fp = fopen($file_path, "a") or die("Unable to open file!");
        fwrite($fp, self::$write_data);
        fclose($fp);
        return $file_path;
    }
}