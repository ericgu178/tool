<?php
namespace tool\SimplyPlaying;
/**
 * 进程
 *
 * @author EricGU178
 */
class Process
{
    /**
     * 获取所有命令进程号
     *
     * @param string $process_name
     * @return void
     * @author EricGU178
     */
    public function getPid($process_name="php test.php")
    {
        $output = shell_exec("ps ax | grep '$process_name' | grep -v grep");
        echo $output;
        $pid = [];
        foreach (explode("\n",$output) as $v) {
            if (empty($v)) {
                continue;
            }
            $pid[] = explode(" ",$v)[0];
        }
        print_r($pid);
        return $pid;
    }

    /**
     * 程序继续
     *
     * @param string $process_name
     * @param object $callback
     * @return void
     * @author EricGU178
     */
    public function _continue($process_name)
    {
        $pid = $this->getPid($process_name);
        if (count($pid) == 0) {
            throw new \Exception('进程号不存在哦');
        }
        foreach ($pid as $key => $value) {
            shell_exec("kill -CONT $value");
        }
    }

    /**
     * 程序暂停
     *
     * @param string $process_name
     * @param object $callback
     * @return void
     * @author EricGU178
     */
    public function _stop($process_name)
    {
        $pid = $this->getPid($process_name);
        if (count($pid) == 0) {
            throw new \Exception('进程号不存在哦');
        }
        foreach ($pid as $key => $value) {
            shell_exec("kill -STOP $value");
        }
    }
}
$p = new Process;

$p->_continue("php test.php");

