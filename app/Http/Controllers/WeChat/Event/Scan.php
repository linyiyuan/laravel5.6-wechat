<?php

namespace App\Http\Controllers\WeChat\Event;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Event;

/**
 * Class Subscribe
 * @package App\Http\Controllers\WeChat\Event
 * 微信扫描事件
*/
class Scan extends Controller
{

    /*
     * 微信实例
     */
    private $app;


    /**
     * 微信推送数据
     */
    private $message;


    /**
     * 初始化
     */
    public function __construct()
    {
        //获取微信实例
        $this->app = app('wechat.official_account');
    }

    /**
     * @param $message
     * 微信关注事件
     */
    public function init($message)
    {
        $this->message = $message;

        $app = $this->app;

        $ticket = Redis::get($message['Ticket']);
        //获取二维码key
        if ($ticket){
            //获取用户id
            $openId = $message['FromUserName'];

            $userinfo = $app->user->get($message['FromUserName']);//获取用户信息

            Redis::set($ticket,json_encode($userinfo));

            Redis::expire($ticket,3600);
        }

    }
}
