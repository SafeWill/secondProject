<?php
namespace app\admin\controller;
use think\Session;
class Index
{
    /**
     *   初始化
     *  判断是否登陆
     */
    public function _initialize()
    {
        if( session('adminuser') == null ) {
//            return view('login/404');
            return $this->error('请先登录');
        }
    }

    public function index()
    {
        return view('index/index');
    }
}
