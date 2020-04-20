<?php
namespace tool;

use tool\Rand;
use tool\Image\Compression;
/**
 * 文件操作类
 *
 * @author EricGU178
 */
class File extends Base
{
    static public $dateFormat = [
        '0'     =>  'Y', // 年
        '1'     =>  'Ym', // 年月 
        '2'     =>  'Ymd' // 年月日
    ];

    public function __construct()
    {
        set_time_limit(0);
    }

    /**
     * 创建文件
     *
     * @param 储存目录 $dir
     * @param 文件名称 $filename
     * @param 数据 $data
     * @param integer $type 文件夹格式
     * @return string
     * @author EricGU178
     */
    static public function create($dir,$filename,$data,$type = 2, $percent = null):string
    {
        $date = Rand::formatDate(self::$dateFormat[$type]);
        $directory = $dir . '/' . $date;
        $file_path = $directory . '/' . $filename;
        if (!is_dir($directory)) {
            mkdir($directory,0777);
        }
        $fp = fopen($file_path , 'a') or die("Unable to open file!");
        fwrite($fp, $data);
        fclose($fp);
        if (!is_null($percent)) { // 压缩图片
            Compression::init($file_path,$percent)->compressImg($file_path);
        }
        return $file_path;
    }

    /**
     * 通过字节流复制文件
     *
     * @param string $path
     * @param string $to_file
     * @return boolean
     * @author EricGU178
     */
    static public function copyFile(string $path, string $to_file):boolean
    {
        try {
            if (!is_readable($path)) {
                throw new \Exception('文件不可读');
            }
            // 没有目录 创建目录
            if (!is_dir(dirname($to_file))) {
                @mkdir(dirname($to_file).'/', 0777, TRUE);
            }
                
            $handle1 = fopen($path, 'r'); // 被复制的文件
            $handle2 = fopen($to_file, 'w'); // 复制的文件 
            if ($handle1 && $handle2) {
                stream_copy_to_stream($handle1, $handle2);
                fclose($handle1);
                fclose($handle2);
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * 遍历文件返回文件数组
     *
     * @param string dir
     * @param time out 超时时间
     * @return void
     * @author EricGU178
     */
    public function listFile($dir = '/log',$time_out = 5)
    {
        $files = [];
        $glob = $this->glob2foreach($dir);
        $start_time = time();
        while ($glob->valid()) {
            if (time() - $start_time > $time_out) {
                break;
            }
            // 当前文件
            $filename = $glob->current();
            $files[] = $filename;
            // 指向下一个，不能少
            $glob->next();
        }
        return $files;
    }


    /**
     * 遍历文件夹下的文件
     *
     * @param string $path
     * @param boolean $include_dirs
     * @return void
     * @author EricGU178
     */
    private function glob2foreach($path, $include_dirs=false) 
    {
        $path = rtrim($path, '/*');
        if (is_readable($path)) {
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (substr($file, 0, 1) == '.') {
                    continue;
                }
                $rfile = $path . '/' . $file;
                if (is_dir($rfile)) {
                    $sub = $this->glob2foreach($rfile, $include_dirs);
                    while ($sub->valid()) {
                        yield $sub->current();
                        $sub->next();
                    }
                    if ($include_dirs) {
                        yield $rfile;
                    }
                } else {
                    yield $rfile;
                }
            }
            closedir($dh);
        }
    }

}