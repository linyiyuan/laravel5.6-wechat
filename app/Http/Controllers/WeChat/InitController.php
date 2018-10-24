<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;

/**
 * Class InitController
 * @package App\Http\Controllers\WeChat
 * 微信服务初始化类
 */
class InitController extends Controller
{
    /**
     * 微信实例
     */
    private $app;

    /**
     * 初始化
     */
    public function __construct()
    {
        //获取微信实例
        $this->app = app('wechat.official_account');
    }

    /**
     * 监听微信推送事件
     */
    public function init()
    {
        //记录日志 表示服务已经启动
        Log::info('request arrived');

        $app = $this->app;


//      //监听微信公众号推送事件
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    //调用事件处理器
                    EventController::init($message);
                    break;
                case 'text':
                    return '收到文字信息';
                    break;
                case 'image':
                    return '收到图片信息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });

        $response = $app->server->serve();

        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 检查微信是否授权
     */
    public function checkSession(Request $request)
    {
        $ticket = $request->ticket;
        if ($ticket){
            $userInfo = Redis::get($ticket);
            if (isset(json_decode($userInfo,true)['nickname'])) {
                return response()->json(['code' => 200 ,'data' => $userInfo ]);
            }else{
                return response()->json(['code' => 400 ,'data' => '微信未授权' ]);
            }

            return response()->json(['code' => 400 ,'data' => '微信未授权' ]);
        }

        return response()->json(['code' => 400 ,'data' => '微信未授权' ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 获取公众号二维码
     */
    public function login(Request $request)
    {
        $app = $this->app;

        $result = $app->qrcode->temporary('foo', 6 * 24 * 3600);

        $ticket = $result['ticket'];//获取二维码的key

        $url = $app->qrcode->url($result['ticket']);

        //保存key到redis中并且给定过期时间
        Redis::set($ticket,$ticket);
        Redis::expire($ticket,3600);

        return view('wechat.login',compact('url','ticket'));
    }
}
