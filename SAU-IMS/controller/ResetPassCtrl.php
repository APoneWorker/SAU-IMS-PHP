<?php

/**
 * Created by PhpStorm.
 * User: APone
 * 创建新的密码
 * Date: 2016/11/26
 * Time: 17:10
 */
defined("APP") or die("error");

class ResetPassCtrl
{
    /**
     * 默认执行函数，这里加载页面
     */
    public function exec()
    {
        if (!empty($_GET)) {//点击邮箱获取的链接
            $time = isset($_GET["t"]) ? htmlspecialchars(trim($_GET["t"], " ")) : "";//获得时间
            $hash = isset($_GET["h"]) ? htmlspecialchars(trim($_GET["h"], " ")) : "";//获得hash
            $sessionId = isset($_GET["sid"]) ? htmlspecialchars(trim($_GET["sid"], " ")) : "";//获得hash

            session_id($sessionId);
            session_start();//打开session
            date_default_timezone_set("PRC");//设置时区

            if ($hash != $_SESSION["hash"]) {//检测链接合法性
                die("Do you know 'gg'?");
            } else if (time() - $time > 20 * 60) {//链接有效期
                die("链接已过期");
            } else {
                require_once VIEW_PATH . "forgetPassword/reset.html";
            }
        }
    }

    /**
     * 点击链接重置密码
     */
    public function resetPass()
    {
        if (!empty($_POST)) {
            $password = isset($_POST["password"]) ? htmlspecialchars(trim($_POST["password"], " ")) : "";//获得新用户名
            $again = isset($_POST["again"]) ? htmlspecialchars(trim($_POST["again"], " ")) : "";//获得再次输入密码
            $resetPassword = new ResetPassword();

            if (empty($password)) {//账号不能为空
                $resetPassword->message = "新密码不能为空";
                die(json_encode($resetPassword));
            } else if (empty($again)) {
                $resetPassword->message = "确认密码不能为空";
                die(json_encode($resetPassword));
            } else if ($password != $again) {
                $resetPassword->message = "两次密码不相同，请重新输入";
                die(json_encode($resetPassword));
            }

            session_start();//打开session
            $userName = $_SESSION["userName"];
            $user = new GeneralUser($userName);
            $resetPassword->success = $user->createPassword(md5(md5($password)));
            if ($resetPassword->success) {
                $resetPassword->url = "?c=ResetPass&a=resetSuccess";
                die(json_encode($resetPassword));
            }
        }
    }

    public function resetSuccess()
    {
        require_once VIEW_PATH . "forgetPassword/haveSet.html";
    }
}


/**
 * Class ForgetPassword
 * 用作json传数据用
 */
class ResetPassword
{
    /**
     * @var string 登陆是否成功
     */
    public $success = false;

    /**
     * @var bool 相关提示信息（错误用）
     */
    public $message = "";

    /**
     * @var string 登陆成功的跳转页面
     */
    public $url = "";
}