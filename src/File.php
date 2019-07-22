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

    static public function create($dir,$filename,$data)
    {
        $date = Rand::formatDate('Ymd');
        $directory = $dir . '/' . $date;
        $file_path = $directory . '/' . $filename;
        if (!is_dir($directory)) {
            mkdir($directory,0777);
        }
        $fp = fopen($file_path , 'a') or die("Unable to open file!");
        fwrite($fp, $data);
        fclose($fp);
        return $file_path;
    }
}