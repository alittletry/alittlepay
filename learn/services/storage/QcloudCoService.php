<?php


namespace learn\services\storage;

use Qcloud\Cos\Client;

/**
 * 腾讯云COS存储
 * Class QcloudCoService
 * @package learn\services\storage
 */
class QcloudCoService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 设置存储位置
     * @var null
     */
    protected $bucket = null;

    /**
     * 配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function options()
    {
        $config = SystemConfigMore(['storage_secretid','storage_secretkey','storage_region','storage_bucket']);
        return [
            'region' => $config['storage_region'],
            'credentials' => [
                'secretId'  => $config['storage_secretid'],
                'secretKey' => $config['storage_secretkey']
            ]
        ];
    }

    /**
     * 创建对象
     * @return Client|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function cos()
    {
        (self::$instance === null) && (self::$instance = new Client(self::options()));
        return self::$instance;
    }

    /**
     * @param string $key
     * @param string $filePath
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function put(string $key, string $filePath)
    {
        try {
            $result = self::cos()->putObject([
                'Bucket' => systemConfig("storage_bucket"),
                'Key' => $key,
                'Body' => file_get_contents($filePath)
            ]);
            return $result ? systemConfig("storage_domain").$key : "";
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return "";
        }
    }

    /**
     * 删除
     * @param string $key
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function del(string $key)
    {
        try {
            return self::cos()->deleteObject([
                'Bucket' => systemConfig("storage_bucket"),
                'Key' => $key,
            ]);
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return "";
        }
    }
}