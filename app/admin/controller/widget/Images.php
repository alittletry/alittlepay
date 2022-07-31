<?php


namespace app\admin\controller\widget;


use app\admin\controller\AuthController;
use app\admin\model\widget\Attachment;
use app\admin\model\widget\AttachmentCategory;
use FormBuilder\Factory\Elm;
use learn\services\FormBuilderService as Form;
use learn\services\storage\QcloudCoService;
use learn\services\UtilService as Util;
use learn\utils\Json;

/**
 * Class Images
 * @package app\admin\controller\widget
 */
class Images extends AuthController
{
    /**
     * 附件类型
     * @var string
     */
    private $type = "image";

    public function index()
    {
        return $this->fetch();
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function category()
    {
        return app("json")->success(AttachmentCategory::buildNodes("image",0,$this->request->param("title","")));
    }

    /**
     * 添加目录
     * @param int $id
     * @param int $pid
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function addCategory($id=0, $pid=0)
    {
        $form = array();
        $form[] = Elm::select('pid','上级分类',(int)$pid?: (int)$id)->options(function (){
            $menu = [];
            $menu[] = ['label'=>"顶级分类","value"=>0];
            $list = AttachmentCategory::getCategoryLst();
            foreach ($list as $value) $menu[] = ['label'=>$value['name'],"value"=>$value['id']];
            return $menu;
        })->col(18);
        $form[] = Elm::input('name','分类名称')->col(18);
        $form[] = Elm::hidden('type','image')->col(18);
        $form = Form::make_post_form($form, url('saveCategory')->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 目录的修改
     * @param $id
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function editCategory($id=0,$pid=0)
    {
        if ($id==0) return app("json")->fail("没有选中分类");
        $form = array();
        $form[] = Elm::select('pid','上级分类',(int)$pid)->options(function (){
            $menu = [];
            $menu[] = ['label'=>"顶级分类","value"=>0];
            $list = AttachmentCategory::getCategoryLst();
            foreach ($list as $value) $menu[] = ['label'=>$value['name'],"value"=>$value['id']];
            return $menu;
        })->col(18);
        $form[] = Elm::input('name','分类名称',AttachmentCategory::getNameById($id))->col(18);
        $form[] = Elm::hidden('type','image')->col(18);
        $form = Form::make_post_form($form, Url('saveCategory',['id'=>$id])->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 保存目录
     * @param string $id
     * @return json
     */
    public function saveCategory($id="")
    {
        $data = Util::postMore([
            ['pid',0],
            ['type','image'],
            ['name','']
        ]);
        if ($data['name'] == '') return app("json")->fail("分类名称不能为空");
        if ($id == "")
        {
            $data['create_user'] = $this->adminId;
            $data['create_time'] = time();
            $res = AttachmentCategory::insert($data);
        }
        else
        {
            $data['update_user'] = $this->adminId;
            $data['update_time'] = time();
            $res = AttachmentCategory::update($data,['id'=>$id]);
        }
        return $res ? app("json")->success("操作成功",'code') : app("json")->fail("操作失败");
    }

    /**
     * 删除目录
     * @param $id
     * @return
     */
    public function delCategory($id)
    {
        if ($id == 0) return app("json")->fail("未选择分类");
        if (Attachment::be($id,"cid")) return app("json")->fail("该分类下有图片不能删除");
        if (AttachmentCategory::be($id,"pid")) return app("json")->fail("该分类下有子分类不能删除");
        return AttachmentCategory::del($id) ? app("json")->success("删除成功") : app("json")->fail("删除失败");
    }

    /**
     * @return mixed
     */
    public function getImageList()
    {
        $where = Util::postMore([
            ['cid',''],
            ['type',$this->type],
            ['page',1],
            ['limit',12]
        ]);
        return app("json")->layui(Attachment::pagination($where));
    }

    /**
     * @param $id
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editImage($id)
    {
        if ($id==0) return app("json")->fail("没有选中图片");
        $image = Attachment::get($id);
        $form = array();
        $form[] = Elm::select('cid','选中分类',(int)$image['cid'])->options(AttachmentCategory::returnOptions())->col(18);
        $form[] = Elm::hidden('type','image')->col(18);
        $form = Form::make_post_form($form, Url('saveImage',['id'=>$id])->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 修改图片分类
     * @param $id
     * @return mixed
     */
    public function saveImage($id)
    {
        return Attachment::update(['cid'=>$this->request->param('cid')],['id'=>$id]) ? app("json")->success("修改成功",'code') : app("json")->fail("修改失败");
    }

    /**
     * 删除图片
     * @param $id
     * @return mixed
     */
    public function delImage($id)
    {
        if ($id == 0) return app("json")->fail("未选择图片");
        $image = Attachment::get($id);
        try {
            switch ($image['storage'])
            {
                case 1:
                    unlink(app()->getRootPath() . 'public'.$image['path']);
                    break;
                case 2:
                    QcloudCoService::del(str_replace(systemConfig("storage_domain"),"",$image['path']));
                    break;
            }
            return Attachment::del($id) ? app("json")->success("删除成功") : app("json")->fail("删除失败");
        }catch (\Exception $e)
        {
            return app("json")->fail("删除失败".$e);
        }
    }
}