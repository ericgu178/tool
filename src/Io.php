<?php
namespace tool;

/**
 * Io操作类
 *
 * @author EricGU178
 */
class Io extends Base 
{
    const FORMAT_JSON   = 'json';
    const FORMAT_XML    = 'xml';

    /**
     * 数据
     * 
     * @var array
     */
    protected $outputData   =   [];

    /**
     * 格式
     *
     * @var string
     */
    protected $formatType   =   self::FORMAT_JSON;

    /**
     * 设置返回数据
     *
     * @param array $data
     * @return this
     */
    public function setData($data = [])
    {
        $this->outputData = $data;
        return $this;
    }

    /**
     * 设置返回数据的格式
     * @param string $formatType
     * @return $this
     */
    public function setResultFormat($formatType = self::FORMAT_JSON)
    {
        $this->formatType = $formatType;
        return $this;
    }

    /**
     * 发送
     *
     * @return void
     * @author EricGU178
     */
    public function send()
    {
        if ($this->formatType == self::FORMAT_JSON) {
            header("Content-Type:text/json");
            $returnStr = json_encode($this->outputData,JSON_UNESCAPED_UNICODE);
        }
        if ($this->formatType == self::FORMAT_XML) {
            header("Content-Type:text/xml");
            $returnStr = $this->toXml($this->outputData);
        }
        echo $returnStr;
        die;
    }

    /*=============================================
     *  xml 操作
     * 
     * ============================================ */

    /**
     * xmlToJson
     *
     * @param  $xmlString
     * @return void
     * @author EricGU178
     */
    static public function xmlToJson($xmlString)
    {
        $result = self::xmlToArray($xmlString);
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }

    /**
     * xmlToArray
     * @param $json string xml 字符串
     * @return mixed
     */
    static public function xmlToArray($xmlString)
    {
        libxml_disable_entity_loader(true);
        @$simpleXml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        return self::buildArray($simpleXml);
    }

    /**
     * toArray
     * @param \SimpleXMLElement $obj
     * @return array
     */
    static protected function buildArray($obj)
    {
        $result = null;
        if (is_object($obj)) {
            $obj = (array)$obj;
        }
        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $res = self::buildArray($value);
                if (('@attributes' === $key) && ($key)) {
                    $result = $res;
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $obj;
        }
        return $result;
    }

    /**
     * xml 编码
     *
     * @return void
     * @author EricGU178
     */
    private function toXml($data)
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $this->buildXml($xml, 'xml', $data);
        return $xml->outputMemory();
    }

    /**
     * 构建xml
     *
     * @param \XMLWriter $xml
     * @param string $rootElement
     * @param object|array $data
     * @return void
     */
    private function buildXml(\XMLWriter $xml,string $rootElement,$data) 
    {
        if(is_object($data)) {
            $data = (array) $data;
        }
        $xml->startElement($rootElement);
        foreach($data as $key => $value) {
            if (is_numeric($key)) {
                throw new \Exception('数组下标不允许出现数字');
            }
            if (is_array($value)) {
                $this->toXml($xml, $key, $value);
            } else {
                $xml->startElement($key);
                $xml->writeCdata($value);
                $xml->endElement();
            }
        }
        $xml->endElement();
    }
    
}