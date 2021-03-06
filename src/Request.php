<?php
namespace tool;

/**
 * 请求类
 *
 * @author EricGU178
 */
class Request extends Base
{
    /**
     * 发起post请求
     *
     * @param string $url
     * @param array $data
     * @return void
     * @author EricGU178
     */
    static public function requestPost(string $url, array $data , $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        $postMultipart = false;
        $postBodyString = '';
        if(is_array($data) == true) {
            // Check each post field
            foreach($data as $key => &$value) {
                // Convert values for keys starting with '@' prefix
                if ("@" != substr($value, 0, 1)) { //判断是不是文件上传
					$postBodyString .= "$key=" . urlencode($value) . "&";
					// $postBodyString .= "$key=" . $value . "&";
                }
                if(strpos($value, '@') === 0) {
                    $postMultipart = true;
                    $filename = ltrim($value, '@');
                    $data[$key] = new \CURLFile($filename);
                }
            }
        }
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        // Post提交的数据包
        if ($postMultipart) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回

        // 看不懂 总之解决了 编码错误的问题
        if (!$postMultipart) {
            array_push($headers,'content-type: application/x-www-form-urlencoded;charset=utf-8'); // 一个用来设置HTTP头字段的数组
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // 一个用来设置HTTP头字段的数组
        }
        
        $response = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话

        return $response; // 返回数据
    }

    /**
     * 发起get请求
     *
     * @param string $url
     * @param array $headers
     * @param bool $isProxy 是否代理
     * @param string $proxy 代理
     * @return void
     * @author EricGU178
     */
    static public function requestGet(string $url, $headers = [],$isProxy = false , $proxy = '')
    {
        $curl = curl_init();
        //设置选项，包括URL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);//绕过ssl验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($isProxy == true) {
            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        //执行并获取HTML文档内容
        $response = curl_exec($curl);
        //释放curl句柄
        curl_close($curl);

        return $response;
    }

    /**
     * 正常的post请求 不会上传文件的那种
     * xml 请求需要传入headers   "Content-type: text/xml"
     *
     * @return void
     * @author EricGU178
     */
    static public function requestNormalPost(string $url, $data, array $headers = [])
    {
        if (is_array($data)) {
            $data = json_encode($data,256);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_POST, 1);   // post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);   // post的变量
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}