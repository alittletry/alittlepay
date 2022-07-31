<?php


namespace app\models\user;


use app\api\model\BaseModel;
use app\api\model\ModelTrait;

/**
 * Class UserBill
 * @package app\models\user
 */
class UserBill extends BaseModel
{
    use ModelTrait;

    /**
     * 用户账单表
     * @param array $order
     * @return int|string
     */
    public static function addBill(array $order)
    {
        $data = [
            'uid' => $order['uid'],
            'source' => $order['source'],
            'oid' => $order['oid'],
            'status' => 1,
            'cost' => $order['pay_price'],
            'io' => 2,
            'remark' => $order['remark'],
            'add_time' => time()
        ];
        return self::insert($data);
    }
}