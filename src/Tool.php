<?php
namespace tool;
/**
 * 工具类
 *
 * @author EricGU178
 */
class Tool extends Base
{
    const MicroMessenger = "MicroMessenger";// 微信
    const AlipayClient  = "AlipayClient"; // 支付宝

    /**
     * 临时缓存图片
     *
     * @param string $file
     * @return void
     * @author EricGU178
     */
    public function cacheImg($file)
    {
        $time = time();
        $filename = ROOT_PATH . 'public' . DS . 'cache' . DS . $time.'.jpg';
        $size = file_put_contents($filename,file_get_contents($file));
        if($size){
            return $filename;
        }
        return false;
    }

    /**
     * 识别http请求源
     *
     * @return void
     * @author EricGU178
     */
    public function getScanSource($user_agent):string
    {
        if (substr_count($user_agent, self::MicroMessenger) != false) {
            return "WECHAT";
        }
        if (substr_count($user_agent, self::AlipayClient) != false) {
            return "ALIPAY";
        }
        return "OTHER";
    }

    /**
     * 补充字符串
     *
     * @param string $ponitStr 指定字符串
     * @param string $str 数据字符串
     * @return void
     * @author EricGU178
     */
    public function supplementStr(string $ponitStr,string $str):string
    {
        if (!substr_count($str, $ponitStr)) {
            return $str . $ponitStr;
        }
        return $str;
    }

    /**
     * 返回路径资源
     *
     * @param [type] $path
     * @return string
     * @author EricGU178
     */
    public function backFileValue($path):string
    {
        if (str_split($path)[0]!= DS ) {
            $path = DS ."{$path}";
        }
        $resouce = fopen( ROOT_PATH . "public{$path}",'r');
        $value = fread($resouce,filesize( ROOT_PATH . "public{$path}"));
        fclose($resouce);
        return $value;
    }

    /**
     * 功能：导出excel(csv)
     * @param $data 导出数据
     * @param $headlist 列名
     * @param $fileName 输出Excel文件名
     * @param $type 写入临时文件 时类型为2 默认为1
     */
    public function csv_export($data = [], $headlist = [], $fileName , $type = 1)
    {
        set_time_limit(0);
        if ($type == 2) {
            $fp = fopen(ROOT_PATH . 'public' . DS . 'cache' . DS . $fileName . '.csv', 'w');
        } else {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
            header('Cache-Control: max-age=0');
            $fp = fopen('php://output', 'a');
        }
        //将数据通过fputcsv写到文件句柄
        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }
        fputcsv($fp, $headlist);
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $num++;
            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }
            $row = $data[$i];
            foreach ($row as $key => $value) {
                $row[$key] = iconv('utf-8', 'gbk', $value);
            }
            fputcsv($fp, $row);
        }
        fclose($fp);  //每生成一个文件关闭
        return ROOT_PATH . 'public' . DS . 'cache' . DS . $fileName . '.csv';
    }

    /**
     * 形成压缩包
     *
     * @param string $zipName 压缩包名字
     * @param array $files 要压缩的文件
     * @return void
     * @author EricGU178
     */
    public function zip($zipName,$files)
    {
        @ob_end_clean();
        $zip = new \ZipArchive();
        $path = ROOT_PATH . 'public' . DS . 'cache' . DS . $zipName;
        $zip->open($path,\ZipArchive::CREATE);   //打开压缩包
        foreach($files as $file){
            $zip->addFile($file,basename($file));   //向压缩包中添加文件
        }
        $zip->close();  //关闭压缩包
        foreach($files as $file){
            unlink($file);
        }
        //输出压缩文件提供下载
        header('Content-Type: application/zip');
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($path));
        header('Content-Disposition: attachment;filename='.basename($path));
        header('Cache-Control: max-age=0');
        readfile($path); // 提供下载
        unlink($path); //删除
    }

    /**
     * 返回url
     *
     * @param string $path
     * @return void
     * @author EricGU178
     */
    static public function getUrl($path="") :string
    {
        $common = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
        if ($path=="") {
            $url = $common . $_SERVER['REQUEST_URI'];
        } else {
            $url = $common . $path;
        }
        return $url;
    }

    // 获取当前周
    static public function get_weeks($date)
    {
        if (\is_numeric($date)) {
            $date = date('Y-m-d',$date);
        }
        $first = 1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w = date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6 
        $now_start = date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $now_end = date('Y-m-d',strtotime("$now_start +6 days"));  //本周结束日期
        return [$now_start,$now_end];
    }

    /**
     * 获取上一周
     *
     * @return void
     * @author EricGU178
     */
    static public function get_last_weeks($data = '')
    {
        if (empty($date)) {
            $date   =   date('Y-m-d');  //当前日期
        }
        if (\is_numeric($date)) {
            $date = date('Y-m-d',$date);
        }
        
        $first  =   1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w      =   date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $now_start  =   date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        $now_end    =   date('Y-m-d',strtotime("$now_start +6 days"));  //本周结束日期
        $last_start =   date('Y-m-d',strtotime("$now_start - 7 days"));  //上周开始日期
        $last_end   =   date('Y-m-d',strtotime("$now_start - 1 days"));  //上周结束日期
        return [$last_start,$last_end];
    }

    /**
     * 生成直接可用的可输出的数组
     *
     * @param string $json
     * @param string $result
     * @return void
     */
    static public function json_decode_to_string($json,$result = "[\n")
    {
        static $level = 1;
        $startT = null;
        $endT = null;
        for ($n=0; $n<$level; $n++) {
            $startT .= "\t";
        }
        for ($n=0; $n<$level-1; $n++) {
            $endT .= "\t";
        }
        $arr = json_decode($json,true);
        foreach ($arr as $key => $val) {
            if (!is_array($val)) {
                $result .= "$startT'$key' => '$val',\n";
            } else {
                $level++;
                $result .= "$startT'$key' => " . self::json_decode_to_string(
                    json_encode($val,256)
                );
                $level--;
            }
        }
        $result .= "$endT],\n";
        return $result;
    }
}