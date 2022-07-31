<?php


namespace app\admin\controller\system;

use app\admin\controller\AuthController;
use app\admin\model\system\SystemConfig as cModel;
use app\admin\model\system\SystemConfigTab as tModel;
use app\Request;
use FormBuilder\Factory\Elm;
use app\api\service\Email;
use learn\services\FormBuilderService as Form;
use learn\services\UtilService as Util;

/**
 * 系统配置
 * Class SystemConfig
 * @package app\admin\controller\system
 */
class SystemConfig extends AuthController
{
    /**
     * 基础配置
     * @param int $tab_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function base($tab_id = 1)
    {
        if ($tab_id < 5)
        {
            $this->assign("tab_id",$tab_id);
            $this->assign("system",cModel::getLstByTabId($tab_id));
            return $this->fetch();
        }else if ($tab_id == 38 || $tab_id == 39)
        {
            $this->assign("tab_id",$tab_id);
            $this->assign("system",cModel::getLstByTabId($tab_id));
            return $this->fetch("pay");
        }
    }

    /**
     * 基础配置
     * @param int $tab_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function wechat($tab_id = 13)
    {
        $this->assign("tab_id",$tab_id);
        $this->assign("system",cModel::getLstByTabId($tab_id));
        return $this->fetch();
    }

    /**
     * 上传配置
     * @return string
     * @throws \Exception
     */
    public function upload()
    {
        return $this->fetch();
    }

    /**
     * 短信配置
     * @return string
     * @throws \Exception
     */
    public function sms()
    {
        return $this->fetch();
    }

    /**
     *邮件配置
     * @return string
     * @throws \Exception
     */
    public function email()
    {
        return $this->fetch();
    }

    /**
     * 列表
     * @param int $tab_id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        $where = Util::postMore([
            ['page',1],
            ['limit',20],
            ['tab_id',0]
        ]);
        return app("json")->layui(cModel::lst($where));
    }

    /**
     * 列表
     * @param int $tab_id
     * @return string
     * @throws \Exception
     */
    public function index($tab_id = 0)
    {
        $this->assign("tab",tModel::get($tab_id));
        return $this->fetch("list");
    }
    /**
     * 添加
     * @param Request $request
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add(Request $request)
    {
        $form = array();
        $form[] = Elm::hidden('tab_id',$request->param("tab_id"))->col(10);
        $form[] = Elm::select('tag_type','标签类型')->options(tagOptions())->col(10);
        $form[] = Elm::select('form_type','表单类型')->options(typeOptions())->col(10);
        $form[] = Elm::input('name','标题名称')->col(10);
        $form[] = Elm::input('form_name','表单名称')->col(10);
        $form[] = Elm::input('value','内容')->col(10);
        $form[] = Elm::number('rank','排序',0)->col(10);
        $form[] = Elm::textarea('param','参数')->col(20);
        $form[] = Elm::textarea('remark','字段备注')->col(20);
        $form[] = Elm::radio('is_show','是否显示',1)->options([['label'=>'隐藏','value'=>0],['label'=>'显示','value'=>1]])->col(10);
        $form[] = Elm::radio('upload_type','上传配置',0)->options([['label'=>'单选','value'=>0],['label'=>'多选','value'=>1]])->col(10);
        $form[] = Elm::radio('status','状态',1)->options([['label'=>'禁用','value'=>0],['label'=>'启用','value'=>1]])->col(10);
        $form = Form::make_post_form($form, url('save')->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 修改
     * @param Request $request
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id='')
    {
        if (!$id) return app("json")->fail("项目id不能为空");
        $info = cModel::get($id);
        if (!$info) return app("json")->fail("没有该项目");
        $form = array();
        $form[] = Elm::hidden('tab_id',$info['tab_id'])->col(10);
        $form[] = Elm::select('tag_type','标签类型',$info['tag_type'])->options(tagOptions())->col(10);
        $form[] = Elm::select('form_type','表单类型',$info['form_type'])->options(typeOptions())->col(10);
        $form[] = Elm::input('name','标题名称',$info['name'])->col(10);
        $form[] = Elm::input('form_name','表单名称',$info['form_name'])->col(10);
        $form[] = Elm::input('value','内容',$info['value'])->col(10);
        $form[] = Elm::number('rank','排序',$info['rank'])->col(10);
        $form[] = Elm::textarea('param','参数',$info['param'])->col(20);
        $form[] = Elm::textarea('remark','字段备注',$info['remark'])->col(20);
        $form[] = Elm::radio('is_show','是否显示',$info['is_show'])->options([['label'=>'隐藏','value'=>0],['label'=>'显示','value'=>1]])->col(10);
        $form[] = Elm::radio('upload_type','上传配置',$info['upload_type'])->options([['label'=>'单选','value'=>0],['label'=>'多选','value'=>1]])->col(10);
        $form[] = Elm::radio('status','状态',$info['status'])->options([['label'=>'禁用','value'=>0],['label'=>'启用','value'=>1]])->col(10);
        $form = Form::make_post_form($form, url('save',["id"=>$id])->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 保存修改
     * @param string $id
     * @return mixed
     */
    public function save($id="")
    {
        $data = Util::postMore([
            ['name',''],
            ['tab_id',0],
            ['tag_type',''],
            ['form_type',''],
            ['form_name',''],
            ['value',''],
            ['rank',1],
            ['remark',''],
            ['param',''],
            ['is_show',1],
            ['upload_type',0],
            ['status',1]
        ]);
        if ($data['name'] == "") return app("json")->fail("标题名称不能为空");
        if ($data['tag_type'] == "") return app("json")->fail("标签类型不能为空");
        if ($data['form_type'] == "") return app("json")->fail("表单类型不能为空");
        if ($data['form_name'] == "") return app("json")->fail("表单名称不能为空");
        if ($id=="")
        {
            $data['create_user'] = $this->adminId;
            $data['create_time'] = time();
            $res = cModel::insert($data);
        }else
        {
            $data['update_user'] = $this->adminId;
            $data['update_time'] = time();
            $res = cModel::update($data,['id'=>$id]);
        }
        return $res ? app("json")->success("操作成功",'code') : app("json")->fail("操作失败");
    }

    /**
     * 提交修改
     * @param Request $request
     * @return
     */
    public function ajaxSave(Request $request)
    {
        try {
            foreach ($request->param() as $k => $v) cModel::editValueByFormName($k,$v);
            return app("json")->success("操作成功");
        }catch (\Exception $e)
        {
            return app("json")->fail("操作失败");
        }

    }
    /**
     * 测试邮箱
     * @param Request $request
     * @return
     */
    public function testemail(Request $request)
    {
        $result = (new Email())->send('测试邮箱内容','测试邮箱标题');
        if($result['code'] ===200){
           return app("json")->success("发送成功"); 
        }else{
            return app("json")->fail("发送失败:".$result['data']);
        }

    }
}