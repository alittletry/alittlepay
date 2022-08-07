<?php


namespace app\admin\controller\admin;

use app\admin\controller\AuthController;
use app\admin\model\admin\Admin as aModel;
use app\admin\model\admin\AdminRole as rModel;
use app\Request;
use learn\services\UtilService as Util;
use FormBuilder\Factory\Elm;
use learn\services\FormBuilderService as Form;
use think\facade\Route as Url;

/**
 * 账号管理
 * Class Admin
 * @package app\admin\controller\admin
 */
class Admin extends AuthController
{
    /**
     * 账号列表
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $this->assign("auths",rModel::getAuthLst());
        return $this->fetch();
    }

    /**
     * 账号列表
     * @param Request $request
     * @return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        $where = Util::postMore([
            ['name',''],
            ['tel',''],
            ['start_time',''],
            ['end_time',''],
            ['status',''],
            ['role_id',''],
            ['trade_status',''],
            ['page',1],
            ['limit',20],
        ]);
        return app("json")->layui(aModel::systemPage($where));
    }

    /**
     * 添加账号
     * @param Request $request
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add(Request $request)
    {
        $form = array();
        $form[] = Elm::input('name','登录账号')->col(10);
        $form[] = Elm::input('nickname','昵称')->col(10);
        $form[] = Elm::frameImage('avatar','头像',Url::buildUrl('admin/widget.images/index',array('fodder'=>'avatar','limit'=>1)))->icon("ios-image")->width('96%')->height('440px')->col(10);
        $form[] = Elm::password('pwd','密码')->col(10);
        $form[] = Elm::input('realname','真实姓名')->col(10);
        $form[] = Elm::select('role_id','角色')->options(function(){
            $list = rModel::getAuthLst();
            $menus=[];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['name']];//,'disabled'=>$menu['pid']== 0];
            }
            return $menus;
        })->col(10);
        $form[] = Elm::input('tel','电话')->col(10);
        $form[] = Elm::email('mail','邮箱')->col(10);
        $form[] = Elm::radio('status','状态',1)->options([['label'=>'启用','value'=>1],['label'=>'冻结','value'=>0]])->col(10);
        $form = Form::make_post_form($form, url('save')->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }

    /**
     * 修改账号
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id="")
    {
        if (!$id) return app("json")->fail("账号id不能为空");
        $ainfo = aModel::get($id);
        if (!$ainfo) return app("json")->fail("没有该账号");
        $form = array();
        $form[] = Elm::input('name','登录账号',$ainfo['name'])->col(10);
        $form[] = Elm::input('nickname','昵称',$ainfo['nickname'])->col(10);
        $form[] = Elm::frameImage('avatar','头像',Url::buildUrl('admin/widget.images/index',array('fodder'=>'avatar','limit'=>1)),$ainfo['avatar'])->icon("ios-image")->width('96%')->height('440px')->col(10);
        $form[] = Elm::password('pwd','密码',$ainfo['pwd'])->col(10);
        $form[] = Elm::input('realname','真实姓名',$ainfo['realname'])->col(10);
        $form[] = Elm::select('role_id','角色',$ainfo['role_id'])->options(function(){
            $list = rModel::getAuthLst();
            $menus=[];
            foreach ($list as $menu){
                $menus[] = ['value'=>$menu['id'],'label'=>$menu['name']];//,'disabled'=>$menu['pid']== 0];
            }
            return $menus;
        })->col(10);
        $form[] = Elm::input('tel','电话',$ainfo['tel'])->col(10);
        $form[] = Elm::email('mail','邮箱',$ainfo['mail'])->col(10);
        $form[] = Elm::radio('status','状态',$ainfo['status'])->options([['label'=>'启用','value'=>1],['label'=>'冻结','value'=>0]])->col(10);
        $form = Form::make_post_form($form, url('save',['id'=>$id])->build());
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
            ['nickname',''],
            ['avatar',''],
            ['pwd',''],
            ['realname',''],
            ['role_id',''],
            ['tel',''],
            ['mail',''],
            ['status','']
        ]);
        if ($data['name'] == "") return app("json")->fail("登录账号不能为空");
        if ($data['pwd'] == "") return app("json")->fail("密码不能为空");
        if ($data['tel'] == "") return app("json")->fail("手机号不能为空");
        if ($data['mail'] == "") return app("json")->fail("邮箱不能为空");
        if (is_array($data['avatar'])) $data['avatar'] = $data['avatar'][0];
        if ($id=="")
        {
            $data['pwd'] = md5(md5($data['pwd']));
            $data['ip'] = $this->request->ip();
            $data['create_user'] = $this->adminId;
            $data['create_time'] = time();
            $res = aModel::insert($data);
        }else
        {
            $ainfo = aModel::get($id);
            if ($ainfo['pwd'] != $data['pwd']) $data['pwd'] = md5(md5($data['pwd']));
            $data['update_user'] = $this->adminId;
            $data['update_time'] = time();
            $res = aModel::update($data,['id'=>$id]);
        }
        return $res ? app("json")->success("操作成功",'code') : app("json")->fail("操作失败");
    }

    /**
     * 修改密码
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function pwd(Request $request)
    {
        return $this->fetch();
    }

    /**
     * 修改密码
     * @param Request $request
     * @return mixed
     */
    public function changePwd(Request $request)
    {
        $data = Util::postMore([
            ['oldpwd',''],
            ['newpwd','']
        ]);
        if ($data['oldpwd'] == '' || $data['newpwd'] == '') return app("json")->fail("参数有误，新旧密码为空！");
        if ($this->adminInfo['pwd'] == md5($data['oldpwd'])) return aModel::update(['pwd'=>md5($data['newpwd'])],['id'=>$this->adminId]) ? app("json")->success("操作成功") : app("json")->fail("操作失败");
        return app("json")->fail("密码不正确！");
    }

    /**
     * 个人信息
     * @return string
     * @throws \Exception
     */
    public function profile()
    {
        $this->assign("info",aModel::get($this->adminId));
        return $this->fetch();
    }

    /**
     * 修改密码
     * @param Request $request
     * @return mixed
     */
    public function changProfile(Request $request)
    {
        $data = Util::postMore([
            ['nickname',''],
            ['avatar',''],
            ['tel',''],
            ['mail',''],
            ['remark','']
        ]);
        if ($data['nickname'] == '' || $data['avatar'] == '' || $data['tel'] == '' || $data['mail'] == '') return app("json")->fail("必选项不能为空！");
        return aModel::update(['nickname'=>$data['nickname'],'avatar'=>$data['avatar'],'tel'=>$data['tel'],'mail'=>$data['mail'],'remark'=>$data['remark']],['id'=>$this->adminId]) ? app("json")->success("操作成功") : app("json")->fail("操作失败");
    }
}