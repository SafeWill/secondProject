<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
class discuss extends Controller
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
        $dis_count = Db::table('discuss')->count();
        $res_dis = Db::table('discuss')->paginate(10);
        $page = $res_dis->render();

        $res_tel = Db::table('user_func')
            ->alias('u')
            ->join('discuss d','u.uid = d.uid')
            ->field('u.tel')
            ->select();
        $res_art = Db::table('article')
            ->alias('a')
            ->join('discuss d','a.aid = d.aid')
            ->field('a.aname')
            ->select();

        $result = [];
        foreach($res_dis as $k=>$v){
            $result[$k]['did'] = $v['did'];
//            $result[$k]['aname'] = $v['name'];
//            $result[$k]['dispaly'] = $v['dispaly'];
            $result[$k]['content'] = $v['content'];
            $result[$k]['time'] = $v['time'];
        }
        foreach($res_tel as $k=>$v){
            $result[$k]['tel'] = $v['tel'];
        }
        foreach($res_art as $k=>$v){
            $result[$k]['aname'] = $v['aname'];
        }

//        var_dump($result);die;
        return view('discuss/index',[
            'result'=>$result,
            'dis_count'=>$dis_count,
            'page'=>$page
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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
        $result = Db::table('discuss')->where('did', $id)->delete();
        return json_decode($result);
    }
}
