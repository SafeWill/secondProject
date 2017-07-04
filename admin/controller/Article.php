<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Article extends Controller
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


    /**

     * 显示新闻列表
     *
     */
    public function index()
    {
        // 用户 user_func   所属类 column
        $article_count = Db::table('article')->count();
        $res_art = Db::table('article')->paginate(15);

//        var_dump($res_art);
        $page = $res_art->render();
        $res_tel = Db::table('user_func')
            ->alias('u')
            ->join('article a','u.uid = a.uid')
            ->field('u.tel')
            ->select();
        $res_column = Db::table('column')
            ->alias('c')
            ->join('article a','c.cid = a.cid')
            ->field('c.cname')
            ->select();

        $result = [];
        foreach($res_art as $k=>$v){
            $result[$k]['aid'] = $v['aid'];
            $result[$k]['aname'] = $v['aname'];
            $result[$k]['dispaly'] = $v['dispaly'];
            $result[$k]['hot'] = $v['hot'];
            $result[$k]['regtime'] = $v['regtime'];
        }
        foreach($res_tel as $k=>$v){
            $result[$k]['tel'] = $v['tel'];
        }
        foreach($res_column as $k=>$v){
            $result[$k]['cname'] = $v['cname'];
        }

//        echo '<pre>';
//        print_r($result);die;
//        echo '</pre>';
// aname amage dispaly regtime hot

        return view('article/index',[
            'result'=>$result,
            'page'=>$page,
            'art_count'=>$article_count
        ]);

    }


    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {

        if (!Request::instance()->isAjax()){
            $this->error('你迷路了吧?', '/');
        }

        $row = Db::table('article_func')
            ->alias('u')
            ->where('u.aid',$id)
            ->find();
        return json($row);

    }




    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $result = Db::table('article')->where('aid',$id)->delete();
        return json_encode($result);
    }


    /*
     * 修改状态 --热门与否
     * */
    public function h_status()
    {
        $id = Request::instance()->param('id');
//        var_dump($id);die;
        $r = model('AdminModel')->status($id, 'article', 'aid', 'hot');
        return $this->index();
    }


    /*
 * 修改状态--审核
 * */
    public function s_status()
    {
        $id = Request::instance()->param('id');
//        var_dump($id);die;
        $r = model('AdminModel')->status($id, 'article', 'aid', 'dispaly');
        return $this->index();
    }

}

