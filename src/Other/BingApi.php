<?php 

/**
 * 必应的接口
 *
 * @author EricGU178
 */
class BingApi
{
    /**
     * 必应获取图片的api
     *
     * @param integer $idx  请求图片截止天数 0 今天 -1 截止中明天 （预准备的） 1 截止至昨天，类推（目前最多获取到7天前的图片）
     * @param integer $n 1-8 返回请求数量，目前最多一次获取8张
     * @param string $mkt 地区
     * @return void
     * @author EricGU178
     */
    public function getBingImg($idx=0,$n=1,$mkt='zh-CN')
    {
        $url = "https://cn.bing.com/HPImageArchive.aspx?format=js&idx=$idx&n=$n&mkt=$mkt";
        $json = file_get_contents($url);
        $json = json_decode($json,true);
        $images = [];
        foreach ($json['images'] as $key => $value) {
            $story = $this->getBingStory($value['startdate']);
            $images[] = [
                'title'     =>  $value['copyright'],
                'url'       =>  'https://cn.bing.com/' . $value['url'],
                'story'     =>  $story['para1'],
                'country'   =>  $story['Country'],
                'city'      =>  $story['City'],
                'continent' =>  $story['Continent']
            ];
        }
        return $images;
    }

    /**
     * 必应获取图片的api
     *
     * @param string $date 日期格式 20180101
     * @return void
     * @author EricGU178
     */
    public function getBingStory($date)
    {
        $url = "https://cn.bing.com/cnhp/coverstory?d=$date";
        $json = file_get_contents($url);
        $json = json_decode($json,true);
        return $json;
    }
}


$a = new BingApi();
$s = $a->getBingImg(-1,8);
print_r($s);