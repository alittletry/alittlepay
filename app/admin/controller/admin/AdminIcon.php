<?php


namespace app\admin\controller\admin;


use app\admin\controller\AuthController;

/**
 * 图标
 * Class AdminIcon
 * @package app\admin\controller\admin
 */
class AdminIcon extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }
}