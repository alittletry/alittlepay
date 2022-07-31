<?php


namespace app\admin\controller\widget;


use app\admin\controller\AuthController;

/**
 * Class Icon
 * @package app\admin\controller\widget
 */
class Icon extends AuthController
{
    public function index()
    {
        return $this->fetch();
    }
}