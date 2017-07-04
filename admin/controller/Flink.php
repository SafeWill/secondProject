<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Flink extends Controller
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

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $flink_count = Db::table('flink')->count();
        $result = Db::table('flink')->paginate(10);
        $page = $result->render();
        return view('flink/index',[
           'result'=>$result,
            'page'=>$page,
            'flink_count'=>$flink_count
        ]);
    }

    /*
     搜索框
      */
    public function search()
    {
//        $search = empty($_GET['search'])?'':$_GET['search'];
        $search = empty($_GET['search'])?'':$_GET['search'];
        $admin = model('AdminModel');
        $ret = $admin->search($search, 'flink', 'fname', 'flink_count', '5');
//        var_dump($ret);die;
        return view('flink/index',[
            'flink_count'=>$ret['listcount'],
            'result'=>$ret['result'],
            'page'=>$ret['page']
        ]);
//        $flink_count = Db::table('flink')->count();
////        var_dump($search);
//        $result = Db::table('flink')->where('fname','like',"%$search%")->paginate(5);
//        $page = $result->render();
//        return view('flink/index',[
//            'flink_count'=>$flink_count,
//            'result'=>$result,
//            'page'=>$page
//        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('flink/add');
    }

    /**
     * 保存新建的链接
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        $p = Request::instance()->post();
//        var_dump($p);die;
        $data = [
            'fname'=>$p['fname'],
            'fpath'=>$p['fpath'],
            'link_addtime'=>date('Y-m-d H:i:m',time())
        ];
        $res = Db::table('flink')->insert($data);
        if( $res ){
            return $this->success('添加成功');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $result = Db::table('flink')->where('fid',$id)->delete();
        return json_encode($result);
    }


    /*
     * 修改状态
     */
    public function f_status()
    {
        $id = Request::instance()->param('id');
        $res = model('AdminModel')->status($id, 'flink', 'fid', 'display');
        return $this->index();
    }

}
