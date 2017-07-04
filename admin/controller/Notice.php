<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Notice extends Controller
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
        $n_count = Db::table('notice')->count();
        $result = Db::table('notice')->paginate(6);
        $page = $result->render();
        return view('notice/index',[
            'result'=>$result,
            'page'=>$page,
            'n_count'=>$n_count
        ]);
    }


    /*
   搜索框
*/
    public function search()
    {
        $search = empty($_GET['search'])?'':$_GET['search'];
        $admin = model('AdminModel');
        $ret = $admin->search($search, 'notice', 'n_name', 'n_count', '5');
//        var_dump($ret);die;
        return view('notice/index',[
            'n_count'=>$ret['listcount'],
            'result'=>$ret['result'],
            'page'=>$ret['page']
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('notice/add');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

       $res = Request::instance()->post();
        $data = [
           'n_name'=>$res['n_name'],
           'n_content'=>$res['n_content'],
           'n_addtime'=>date('Y-m-d H:i:m', time())
       ];
        $result = Db::table('notice')->insert($data);
//        var_dump($data);
        if ($result) {
            return $this->success('添加成功', url('notice/create'));
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
        $result = Db::table('notice')->where('nid',$id)->find();
//        var_dump($result);
        return view('notice/edit',[
            'res'=>$result
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        $res1 = Request::instance()->param();
//        var_dump($result);die;
        $data = [
            'n_name'=>$res1['n_name'],
            'n_content'=>$res1['n_content'],
            'n_addtime'=>date('Y-m-d H:i:m', time())
        ];
        $result = Db::table('notice')->where('nid',$res1['id'])->update($data);
//        var_dump($result);die;
        if ($result) {
            return $this->success('修改成功', url('notice/index'));
        }

    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        $res = Db::table('notice')->where('nid', $id)->delete();
        return json_decode($res);
    }

    /**
     * 修改状态 - 显示 隐藏
     */
    public function n_status()
    {
        $id = Request::instance()->param('id');
        $r = model('AdminModel')->status($id, 'notice', 'nid', 'display');
        return $this->index();
    }
}
