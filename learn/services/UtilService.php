<?php


namespace learn\services;

/**
 * UTIL
 * Class UtilService
 * @package learn\services
 */
class UtilService
{
    /**
     * post数据
     * @param $params
     * @param null $request
     * @param bool $suffix
     * @return array
     */
    public static function postMore($params,$request = null,$suffix = false)
    {
        if($request === null) $request = app('request');
        $p = [];
        $i = 0;
        foreach ($params as $param){
            if(!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $request->param($param);
            }else{
                if(!isset($param[1])) $param[1] = null;
                if(!isset($param[2])) $param[2] = '';
                $name = is_array($param[1]) ? $param[0].'/a' : $param[0];
                $p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $param[0])] = $request->param($name,$param[1],$param[2]);
            }
        }
        return $p;
    }

    /**
     * get数据
     * @param $params
     * @param null $request
     * @param bool $suffix
     * @return array
     */
    public static function getMore($params,$request=null,$suffix = false)
    {
        if($request === null) $request = app('request');
        $p = [];
        $i = 0;
        foreach ($params as $param){
            if(!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $request->param($param);
            }else{
                if(!isset($param[1])) $param[1] = null;
                if(!isset($param[2])) $param[2] = '';
                $name = is_array($param[1]) ? $param[0].'/a' : $param[0];
                $p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $param[0])] = $request->param($name,$param[1],$param[2]);
            }
        }
        return $p;
    }
}