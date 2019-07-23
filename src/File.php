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

    public function __construct()
    {
        set_time_limit(0);
    }

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