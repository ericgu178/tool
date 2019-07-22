<?php 

/**
 * football-data的接口
 *
 * @author EricGU178
 */
class FootballApi
{
    /**
     * 列出当前所有的欧洲联赛
     *
     * @return void
     * @author EricGU178
     */
    public function competitions($id="")
    {
        $url = "http://api.football-data.org/v2/competitions/$id";
        $json = file_get_contents($url);
        $json = json_decode($json,true);
        print_r($json);
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


$a = new FootballApi();
$s = $a->competitions(2000);
print_r($s);