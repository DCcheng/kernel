<?php
/**
 *  FileName: Maps.php
 *  Description :
 *  Author: DC
 *  Date: 2019/5/27
 *  Time: 9:29
 */


namespace Kernel\Support;

use Kernel\Support\Curl;

class Maps
{
    public $key = "3cc53937e45ed9ab6f2419dc4e684950";
    public $curl;

    //实例化地图扩展，并且开启CURL的SSL请求
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
        $this->curl->ssl = true;
    }

    //根据地址以及对应的城市名获取GPS坐标，城市需要用中文的“广州市”
    public function getGps($address,$city = null)
    {
        $params = [
            "key"=>$this->key,
            "address"=>$address
        ];
        if($city != null)
            $params["city"] = $city;
        $url = $this->setUrl("https://restapi.amap.com/v3/geocode/geo?parameters",$params);
        $result = $this->curl->get($url);
        $json = json_decode($result,true);
        if($json["status"] == 1 && isset($json['geocodes'][0]['location'])){
            return explode(",",$json['geocodes'][0]['location']);
        }else{
            return ['0','0'];
        }
    }

    //设置请求URL地址
    public function setUrl($url,$params){
        $paramsArr = array();
        foreach ($params as $key => $param){
            $paramsArr[] = $key."=".$param;
        }
        $url = $url."&".implode("&",$paramsArr);
        return $url;
    }
}
