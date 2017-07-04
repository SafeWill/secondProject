<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/28 0028
 * Time: 下午 8:10
 */
namespace app\admin\model;
use think\Db;
use \think\Model;

class AdminModel extends Model
{
    /**
     * @param $search  搜索的内容
     * @param $tb      搜索的表名
     * @param $fileds  搜索的字段名
     * @param $count   统计表中信息条数
     * @param $listcount  每页显示数据
     */
    public function search($search, $tb, $fields, $count, $listcount)
    {
        $listcount = Db::table($tb)->count();
        $result = Db::table($tb)->where($fields, "like", "%$search%" )->paginate($listcount);
        $page = $result->render();
        $data= [
            'listcount' =>$listcount,
            'result' =>$result,
            'page'=>$page,
        ];

        return $data;
    }



    /**
     * @param $id       修改的状态对应的id
     * @param $tb         对应的表
     * @param $fields1    字段1
     * @param $fields2    字段2
     * @return int
     */
    public function status($id, $tb ,$fields1, $fields2)
    {
//        $res_pow = Db::table('user_func')->where('uid',$id)->update(['status'=>$power]);

        $res = Db::table($tb)->where($fields1,$id)->find();
        $res = $res[$fields2] ==1?'0':'1';
//        var_dump($res);die;
        $result = Db::table($tb)->where($fields1,$id)->update([$fields2=>$res]);
        return $result;

    }

}