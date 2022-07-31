<?php
// 应用公共文件

if (!function_exists('systemConfigMore'))
{
    /**
     * 获取系统配置值
     * @param array $formNames
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function systemConfigMore(array $formNames): array
    {
        $res = \app\admin\model\system\SystemConfig::getValuesByFormNames($formNames);
        $data = [];
        foreach ($res as $k=>$v) $data[$v['form_name']] = $v['value'];
        return $data;
    }
}

if (!function_exists('paramToArray'))
{
    /**
     * 参数分割成数组
     * @param string $param
     * @param string $delimiter
     * @return array
     */
    function paramToArray(string $param, string $delimiter = "&"): array
    {
        $arr = [];
        foreach (explode($delimiter,$param) as $value)
        {
            $tmp = explode("=",$value);
            $arr[$tmp[0]] = $tmp[1];
        }
        return $arr;
    }
}

if (!function_exists('getFileType'))
{
    /**
     * 获取文件类型
     * @param string $mime
     * @return string
     */
    function getFileType(string $mime): string
    {
        if (stristr($mime,'image')) return 'image';
        elseif (stristr($mime,'video')) return 'video';
        elseif (stristr($mime,'audio')) return 'audio';
    }
}

if (!function_exists('systemConfig'))
{
    /**
     * 获取系统配置值
     * @param string $formName
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function systemConfig(string $formName): string
    {
        return \app\admin\model\system\SystemConfig::getValueByFormName($formName);
    }
}

if (!function_exists('createOrderId'))
{
    /**
     * 创建订单id
     * @return string
     */
    function createOrderId(): string
    {
        return "O".date("YmdHis").rand(1000,9999);
    }
}

if (!function_exists('unicodeEncode'))
{
    /**
     * 中文转unicode
     * @param $str
     * @return string
     */
    function unicodeEncode($str)
    {
        $strArr = preg_split('/(?<!^)(?!$)/u', $str);
        $resUnicode = '';
        foreach ($strArr as $str)
        {
            $bin_str = '';
            $arr = is_array($str) ? $str : str_split($str);
            foreach ($arr as $value) $bin_str .= decbin(ord($value));
            $bin_str = preg_replace('/^.{4}(.{4}).{2}(.{6}).{2}(.{6})$/', '$1$2$3', $bin_str);
            $unicode = dechex(bindec($bin_str));
            $_sup = '';
            for ($i = 0; $i < 4 - strlen($unicode); $i++) $_sup .= '0';
            $str =  '\\u' . $_sup . $unicode;
            $resUnicode .= $str;
        }
        return $resUnicode;
    }
}

if (!function_exists('unicodeDecode'))
{
    /**
     * unicode转中文
     * @param $unicode_str
     * @return string
     */
    function unicodeDecode($unicode_str)
    {
        $json = '{"str":"'.$unicode_str.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }
}