<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

/**
 * Class EventController
 * @package App\Http\Controllers\WeChat
 * 微信事件类
 */
class EventController extends Controller
{
    /**
     * 微信实例
     */
    private static $app;


    /**
     * EventController constructor。
     * 初始化
     */
    public function __construct()
    {
        //获取微信实例
        $this->app = app('wechat.official_account');
    }

    /**
     * @param $message
     * 处理微信不同事件类型
     */
    public static function init($message)
    {
        $event = ucfirst(strtolower($message['Event']));

        $eventClientClass = '\\App\\Http\\Controllers\\WeChat\\Event\\'. $event;

        if (!class_exists($eventClientClass)) {
            //记录日志
            Log::info('Cant load "'. $eventClientClass .'" by wechat');
            return false;
        }

        //调用事件
        $event = new $eventClientClass();
        $event->init($message);

    }
}
