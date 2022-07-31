<?php

use think\facade\Cache;


if (!function_exists('unCamelize'))
{
    /**
     * 驼峰法转下划线
     * @param $camelCaps
     * @param string $separator
     * @return string
     */
    function unCamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}

if (!function_exists('authIsExit'))
{
    /**
     * 判断授权信息是否存在
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function authIsExit(int $adminId): bool
    {
        return Cache::store('redis')->has('store_'.$adminId);
    }
}

if (!function_exists('removeCache'))
{
    /**
     * 判断授权信息是否存在
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    function removeCache(string $path): bool
    {
        $res = true;
        if(is_dir($path)){
            if ($handle = opendir($path)) {
                while (false !== ($item = readdir($handle))) {
                    if ($item != '.' && $item != '..') {
                        if (is_dir($path . '/' . $item)) removeCache($path . '/' . $item);
                        else unlink($path . '/' . $item);
                    }
                }
                closedir($handle);
                if (rmdir($path)) $res = true;
            }
        }
        return $res;
    }
}

if (!function_exists('languageOptions'))
{
    /**
     * 获取编程语言列表
     * @return array
     */
    function languageOptions(): array
    {
        $menus[] = ['value'=>"PHP",'label'=>"PHP"];
        $menus[] = ['value'=>"PYTHON",'label'=>"PYTHON"];
        $menus[] = ['value'=>"C#",'label'=>"C#"];
        $menus[] = ['value'=>"小程序",'label'=>"小程序"];
        $menus[] = ['value'=>"HTML",'label'=>"HTML"];
        $menus[] = ['value'=>"JAVASCRIPT",'label'=>"JAVASCRIPT"];
        $menus[] = ['value'=>"MYSQL",'label'=>"MYSQL"];
        $menus[] = ['value'=>"C语言",'label'=>"C语言"];
        $menus[] = ['value'=>"C++",'label'=>"C++"];
        return $menus;
    }
}

if (!function_exists('toIntArray'))
{
    /**
     * 字符串数组转int数组
     * @return array
     */
    function toIntArray(array $str): array
    {
        foreach ($str as $k=>$v) $str[$k] = (int)$v;
        return $str;
    }
}

if (!function_exists('tagOptions'))
{
    /**
     * 获取form标签
     * @return array
     */
    function tagOptions(): array
    {
        $menus[] = ['value'=>"input",'label'=>"input"];
        $menus[] = ['value'=>"textarea",'label'=>"textarea"];
        $menus[] = ['value'=>"select",'label'=>"select"];
        return $menus;
    }
}

if (!function_exists('typeOptions'))
{
    /**
     * 获取form标签
     * @return array
     */
    function typeOptions(): array
    {
        $menus[] = ['value'=>"text",'label'=>"text"];
        $menus[] = ['value'=>"radio",'label'=>"radio"];
        $menus[] = ['value'=>"password",'label'=>"password"];
        $menus[] = ['value'=>"checkbox",'label'=>"checkbox"];
        $menus[] = ['value'=>"number",'label'=>"number"];
        $menus[] = ['value'=>"hidden",'label'=>"hidden"];
        $menus[] = ['value'=>"email",'label'=>"email"];
        $menus[] = ['value'=>"tel",'label'=>"tel"];
        $menus[] = ['value'=>"date",'label'=>"date"];
        $menus[] = ['value'=>"file",'label'=>"file"];
        return $menus;
    }
}