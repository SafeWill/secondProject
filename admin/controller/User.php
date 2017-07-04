<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
class User extends Controller
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


        $res = Db::table('user_func')->paginate(6);
        $page = $res->render();
//        dump($res);exit;
        $this->assign('res', $res);
        $this->assign('page', $page);
        $count = db('user_func')->count();
        $this->assign('user_count',$count);
        return view('user/list');
    }


    public function read($id)
    {

        $row = Db::table('user_self')
                ->alias('s')
//              ->join('user_func u', 's.uid = u.uid')
                ->where('s.uid',$id)
//              ->field('s.nickname','s.email')
                ->find();
//        return dump($row);

        if ($row === null) {
            $info['status'] = false;
            $info['title'] = '查无数据';
            $info['datas'] = '查无数据,请重试!';
        } else {
            $info['status'] = true;
            $info['title'] = $row['nickname'];
            $info['datas'] = $row;
        }

        return json($info);

    }

    public function create()
    {
        return view('user/add');
    }

    public function add()
    {
        $p = Request::instance()->post();
        $data = [
            'tel'=>$p['tel'],
            'pwd'=>md5($p['pwd']),
            'regtime'=>time()

        ];

        $res = Db::table('user_func')->insert($data);
        if( $res ) {
            return $this->success('添加成功');
        }
    }



    /*
搜索框
*/
    public function search()
    {
        $search = empty($_GET['search'])?'':$_GET['search'];
        $admin = model('AdminModel');
        $ret = $admin->search($search, 'user_func', 'tel', 'user_count', '9');
//        var_dump($ret);die;
        return view('user/list',[
            'user_count'=>$ret['listcount'],
            'res'=>$ret['result'],
            'page'=>$ret['page']
        ]);
    }


    /**
     * 修改用户状态
     */
    public function stop()
    {
        $id = Request::instance()->param('id');
        $power = Db::table('user_func')->where('uid',$id)->field('status')->select();
//        var_dump($power);
        $power = $power[0]['status'] == 1?0:1;
//        var_dump($power);
        $res_pow = Db::table('user_func')->where('uid',$id)->update(['status'=>$power]);
//        var_dump($res_pow);
        return $this->index();

    }

}
