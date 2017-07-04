<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Admin extends Controller
{
    /**
     *   初始化
     *  判断是否登陆
     */
    public function _initialize()
    {

        if( session('adminuser') == null ) {
            return $this->error('请先登录');
        }
    }

    public function index()
    {
        $count = db('admin')->count();
        $this->assign('admin_count',$count);
        return view('admin/list');
    }

    public function read()
    {
        $count = db('admin')->count();
        $result = Db::table('admin')->paginate(5);
        $page = $result->render();
        $this->assign('admin_count',$count);
        $this->assign('result', $result);
        return view('admin/list',[
            'admin_count'=>$count,
            'result'=>$result,
            'page'=>$page
        ]);
    }

    public function create()
    {
        return view('admin/add');
    }

    /*
     * 添加管理员
     * */
    public function add(Request $request)
    {
        date_default_timezone_set('PRC');
        $p = Request::instance()->post();
        $data = [
            'name'=>$p['name'],
            'password'=>md5($p['password']),
            'addtime'=>date('Y-m-d H:i:m',time())
        ];

        $res = Db::table('admin')->insert($data);
        if( $res ) {
            return $this->success('添加成功');
        }

    }


    public function delete($id)
    {
        $result = Db::table('admin')->where('id',$id)->delete();
        return json_encode($result);

    }

    /*
       搜索框
  */
    public function search()
    {
        $search = empty($_GET['search'])?'':$_GET['search'];
        $admin = model('AdminModel');
        $ret = $admin->search($search, 'admin', 'name', 'admin_count', '5');
//        var_dump($ret);die;
        return view('admin/list',[
            'admin_count'=>$ret['listcount'],
            'result'=>$ret['result'],
            'page'=>$ret['page']
        ]);
    }


    /*
     * 禁用管理员
     * */
    public function stop()
    {
        $id = Request::instance()->param('id');
        $power = Db::table('admin')->where('id',$id)->field('power')->select();
//        var_dump($power);
        $power = $power[0]['power'] == 1?0:1;
//        var_dump($power);
        $res_pow = Db::table('admin')->where('id',$id)->update(['power'=>$power]);
//        var_dump($res_pow);
        return $this->read();

    }


}
