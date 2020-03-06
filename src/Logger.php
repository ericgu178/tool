<?php

namespace tool;

/**
 * 日志类
 *
 * @author EricGU178
 */
class Logger extends Base
{
    /**
     * 日志根目录
     *
     * @var string
     */
    protected $dir = '/log';

    // 日志后缀
    const SUFFIX = '.log';

    // 日志级别，由轻微到严重分别是debug（调试）、info（信息）、notice（留意）、warning（警告）、error（错误）
    const DEBUG   = 'debug';
    const INFO    = 'info';
    const NOTICE  = 'notice';
    const WARNING = 'warning';
    const ERROR   = 'error';

    /**
     * 目录
     */
    private $directory = null;

    /**
     * 头部信息
     */
    private $header = null;
    
    static public function dir($dirName)
    {
        return self::instance()->setDir($dirName);
    }

    /**
     * 设置总目录
     *
     * @param string $dirName
     * @return void
     * @author EricGU178
     */
    private function setDir($dirName)
    {
        $this->directory = $this->dir . '/' . $dirName . '/' . date('Ym');
        return $this;
    }

    /**
     * 兼容一下header
     *
     * @param string $header
     * @return void
     * @author EricGU178
     */
    public function setHeader($header)
    {
        return $this->header($header);
    }

    /**
     * 设置头部信息
     *
     * @param string $header
     * @return void
     * @author EricGU178
     */
    public function header($header)
    {
        $this->header = $this->toString($header);
        return $this;
    }


    public function __call($name, $arguments)
    {
        if (in_array($name, [self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR])) {
            $content = $arguments[0];
            $this->write($content, $name);
        }
    }

    /**
     * 写入日志
     *
     * @param string|array $content
     * @param string $level
     * @return void
     * @author EricGU178
     */
    public function write($content, $level)
    {
        $file = date('Ymd');
        if (in_array($level, [self::DEBUG, self::INFO, self::NOTICE, self::WARNING, self::ERROR]) === false) {
            \trigger_error('日志级别错误');
        }
        if ($this->header === null) {
            \trigger_error('没有日志头信息');
        }
        $ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $content = $this->toString($content);
        $date_time = date('Y-m-d H:i:s');
        // 开始写入
        try {
            if (is_dir($this->directory) === false) {
               mkdir($this->directory, 0777, true);
            }

            // 构建写入字符串
            $str = "[记录时间] " . $date_time . "\t[操作IP] " . $ip . "\t——————————————————————————————————————\t" . $this->getHeaderStyle($level,$this->header) .  "\n";
            $str .= $content . "\n" . $this->getHeaderStyle($level,'<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<') . "\n";
            // 日志
            $fp = fopen($this->directory . '/' . $file . self::SUFFIX, "a") or die("Unable to open file!");
            fwrite($fp, $str);
            fclose($fp);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    /**
     * 返回格式
     *
     * @param string|array $content
     * @return void
     * @author EricGU178
     */
    private function toString($content)
    {
        // 异常类自动转换文本
        if ($content instanceof \Exception) {
            $content = date('Y-m-d H:i:s') . ' - line ' . $content->getLine() . ' in ' . $content->getFile() . ':<span style="color:red;">' . $content->getMessage() . "</span><br>\n" . $content->getTraceAsString();
        }
        return json_encode($content,256);
    }

    // 获取样式
    private function getHeaderStyle($level,$header)
    {
        $result = null;
        switch ($level) {
            case self::DEBUG:
                $result = "\033[1;36m " . $header . " \033[0m";
                break;
            case self::INFO:
                $result = "\033[1;32m " . $header . " \033[0m";
                break;
            case self::NOTICE:
                $result = "\033[1;33m " . $header . " \033[0m";
                break;
            case self::WARNING:
                $result = "\033[1;35m " . $header . " \033[0m";
                break;
            case self::ERROR:
                $result = "\033[1;31m " . $header . " \033[0m";
                break;
        }
        return $result;
    }
}