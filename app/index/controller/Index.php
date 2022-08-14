<?php
namespace app\index\controller;

use learn\basic\index\BaseController;


/**
 * Class Index
 * @package app\index\controller
 */
class Index extends BaseController
{
    /**
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        return 'Hello Wrold!';
    }
    public function testnotify()
    {
        return 'success';
    }
}
