<?php


namespace learn\subscribes;

use app\admin\model\wechat\WechatMedia;

/**
 * 资源
 * Class WechatMediaSubscribe
 * @package learn\subscribes
 */
class WechatMediaSubscribe
{
    /**
     * 上传素材之后
     * @param $event
     */
    public function onUploadMediaAfter($event)
    {
        list($res,$path,$temporary) = $event;
        WechatMedia::saveData(['type'=>$res['type'],'media_id'=>$res['media_id'],'create_time'=>$res['created_at'],'path'=>$path,'temporary'=>$temporary]);
    }

    /**
     * 文章素材图片
     * @param $event
     */
    public function onUploadMaterialAfter($event)
    {
        list($res,$path,$temporary) = $event;
        WechatMedia::saveData(['type'=>'material_image','media_id'=>$res['media_id'],'create_time'=>time(),'path'=>$path,'temporary'=>$temporary]);
    }
}