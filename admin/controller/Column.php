<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class Column extends Controller
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
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $result = Db::table('column')->where('pid', 0)->paginate(5);
        $page = $result->render();
//        var_dump($result);
        return view('column/index',[
            'result'=>$result,
            'page'=>$page
        ]);
    }


    public function index2($id)
    {
        $result = Db::table('column')->where('pid',$id)->paginate(10);
        $page = $result->render();
        return view('column/index', [
            'result'=>$result,
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
        return view('column/add');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('photo');
        if (!$file) {
            return $this->error('请添加图片');
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public/static' . DS . 'uploads');
        $info = '/static/uploads/'.str_replace('\\',"/",$info->getSaveName());
        $res = Request::instance()->param();
//        var_dump($res);
        $data = [
            'cname'=>$res['cname'],
            'pid'=>$res['pid'],
            'display'=>$res['display'],
            'hot'=>$res['hot'],
            'photo'=>$info
        ];

//        var_dump($data);
        $result = Db::table('column')->insert($data);
        if ($result) {
            return $this->success('添加成功','admin/column/index');
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
        $id = Request::instance()->param();
//        var_dump($id);die;
        $result = Db::table('column')->where('cid',$id)->find($id);
//        var_dump($result);
        return view('column/edit',[
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
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('photo');
        if (!$file) {
            return $this->error('请添加图片');
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->move(ROOT_PATH . 'public/static' . DS . 'uploads');
        $info = '/static/uploads/'.str_replace('\\',"/",$info->getSaveName());
        $res = Request::instance()->param();
//        var_dump($res);die;
        $data = [
            'cname'=>$res['cname'],
            'pid'=>$res['pid'],
            'display'=>$res['display'],
            'hot'=>$res['hot'],
            'photo'=>$info
        ];

        $res = Db::table('column')->where('cid',$res['id'])->update($data);
//        dump($res);
        if ($res) {
            return $this->success('修改成功','admin/column/index');
        }
    }

    /*
     * 添加子分类
     * */
    public function adds()
    {
        $cid = Request::instance()->param();
//        dump($cid['id']);
//        $pid = Db::table('column')->where('pid',$cid['id'])->find();
        return view('column/adds',[
            'cid'=>$cid
        ]);

    }


    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 查子类
        $res = Db::table('column')->where('pid',$id)->select();
        if ($res) {
            return $this->error('删除失败,请先删除子分类');
        }

//        查该类下有没有文章
        $art = Db::table('article')
                ->alias('a')
                ->where('a.cid',$id)
                ->select();
//        var_dump($art);die;
        if ($art) {
            return $this->error('删除失败,请先删除该类下的文章');
        } else {
            $res1 = Db::table('column')->where('cid',$id)->delete();
            return $this->success('删除成功','admin/column/index');
        }



    }

    /*
       修改显示或隐藏
      */
    public function dstop()
    {
        $id = Request::instance()->param('id');
        $res = model('AdminModel')->status($id, 'column', 'cid', 'display');
        return $this->index();
    }

    /*
      修改是否热门
     */
    public function hstop()
    {
        $id = Request::instance()->param('id');
        $res = model('AdminModel')->status($id, 'column', 'cid', 'hot');
        return $this->index();
    }
}
