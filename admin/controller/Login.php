<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
class Login extends Controller
{
    /*
     加载后台的登录模版
     * */
    public function index()
    {
        Session::clear();
        if (Session::get('adminuser')) {
            return view('index/index');
        }
        return view('login/index');
    }

    /*
     管理员注册
      */
    public function add()
    {
        $p = Request::instance()->post();
        $p['name'] = htmlentities($p['name']);
//        dump($name);die;
        $name = Db::table('admin')->where('name',$p['name'])->find();
        if ($name) {
            return $this->error('用户名已存在');
        }
        if (!($p['password'] == $p['repass'])) {
            return $this->error('两次密码不一致');
        }
        $data = [
            'name' => $p['name'],
            'password' => md5($p['password']),
            'addtime' => date('Y-m-d H:i:m', time())
        ];

        $res = Db::table('admin')->insert($data);
        if (!$res) {
            return $this->error('注册失败');
        }

        return $this->success('注册成功', 'login/index');
//        $res = Db::table('admin')->where('name', $data['name'])->find();
//        var_dump($res);exit;
    }

    /*
     后台登录判断
     * */
    public function check()
    {
        $name = $_POST['name'];
        $name = htmlentities($_POST['name']);
        $password = $_POST['password'];
        if (!trim($name)) {
            return $this->error('用户名不能为空');

        }
        if (!trim($password)) {
            return $this->error('密码不能为空');
        }

        $res = Db::table('admin')->where('name', $name)->where('power','1')->find();

        if (!$res) {
            return $this->error('用户名不存在');
        }
        $result = Db::table('admin')->where('name', $name)->value('password');
//        dump(!($result == md5($password)) );
//        dump($result);exit;
        if (!($result == md5($password))) {
//            dump(md5($password));exit;
            return $this->error('密码不正确');
        } else {
            Session::clear();
            Session::set('adminuser', $name,'think');
            //调用index方法 跳到后台首页
            $this->redirect('login');
        }

    }

    /**
     * 加载后台首页模版
     */
    public function login()
    {
        if (empty(Session::get('adminuser','think'))) {
//        404页面
            return view('login/404');
        }
        // 赋值并加载模版
        $adminuser = Session::get('adminuser','think');
//        $this->assign('adminuser', $adminuser);
        return $this->fetch('index/index', [
            'adminuser' => $adminuser,
        ]);
    }

    /*
     * 加载后台首页的主体  (统计信息 服务器信息)
     */
    public function main()
    {
        $tel = empty($_GET['phone']) ? '' : $_GET['phone'];
        // var_dump($tel);die;

        $host = "http://jshmgsdmfb.market.alicloudapi.com";
        $path = "/shouji/query";
        $method = "GET";
        $appcode = "a54e75949ce548daafb87c217922afd9";
        $headers = array();
        array_push($headers, "Authorization:APPCODE a54e75949ce548daafb87c217922afd9");
        // $querys = "shouji=". $tel;
        $bodys = "";
        $url = $host . $path . "?shouji=" . $tel;
        // $url = "http://jshmgsdmfb.market.alicloudapi.com/?key=".$apikey . $querys;

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        // var_dump(curl_exec($curl));

        $data = curl_exec($curl);  //json形式
        if (!$data) {
            $this->assign('province','');
            $this->assign('city','');
            $this->assign('company','');
        } else {
            $jsonObj = json_decode($data);    //json_decode  转为数组
            $list = $jsonObj->result;

//        var_dump($list);
            if (empty($list)) {
                $this->assign('province','');
                $this->assign('city','');
                $this->assign('company','');
            }else {
                $city = $list->city;
                $province = $list->province;
                $company = $list->company;
                $this->assign('province',$province);
                $this->assign('city',$city);
                $this->assign('company',$company);
            }

        }

        ///////////////////////////////////////////////////////////

        //公告
        $notice = Db::table('notice')->where('display','1')->select();
//        var_dump($notice);

        //服务器信息
        $info = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            '主机名' => $_SERVER['SERVER_NAME'],
            '服务器端口' => $_SERVER['SERVER_PORT'],
            '服务器系统目录' => $_SERVER['SystemRoot'],
            '网站文档目录' => $_SERVER["DOCUMENT_ROOT"],
            '浏览器信息' => substr($_SERVER['HTTP_USER_AGENT'], 0, 40),
            '服务器时间' => date("Y年n月j日 H:i:s"),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            'IP' => gethostbyname($_SERVER['SERVER_NAME']),
            '服务器域名' => $_SERVER['SERVER_NAME'],
            '剩余空间' => round((disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            '服务器语言种类' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            '系统类型及版本号' => php_uname()
        );
        $this->assign('info', $info);

        //统计信息
//        $adminuser = Session::get('adminsuer');
        $artcount = db('article')->count();     //文章
        $usercount = db('user_func')->count();   //用户
        $admincount = db('admin')->count();      //管理员
        $flinkcount = db('flink')->count();      //友情链接
        $noticecount = db('notice')->count();    //公告
        $adverticount = db('adverti')->count();    //广告
        $gamecount = db('game')->count();    //游戏
        $musiccount = db('music')->count();    //音乐
        $carcount = db('carousel')->count();    //轮播图
//        $this->assign('admin_count',$admincount);
        return $this->fetch('login/welcome', [
//            'adminuser' => $adminuser,
            'notice' => $notice,
            'art_count' => $artcount,
            'user_count' => $usercount,
            'admin_count' => $admincount,
            'flink_count' => $flinkcount,
            'notice_count'=>$noticecount,
            'adverti_count'=>$adverticount,
            'game_count'=>$gamecount,
            'music_count'=>$musiccount,
            'car_count'=>$carcount
        ]);

    }

    /*
     退出后台
     * */
    public function out()
    {
//        退出时清空session
        Session::clear('think');
//        dump($_SESSION);exit;
        return view('login/index');
    }
}



