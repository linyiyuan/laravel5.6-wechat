<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WeChatController extends Controller
{

    protected  $userInfo;
    /**
     * 服务器验证
     */
    public function server()
    {
        Log::info('request arrived');

        $app = app('wechat.official_account');

        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                      $ticket = $message['Ticket'];//获取扫描二维码的key
                      $openId = $message['FromUserName'];//获取openid
                      $event = $message['Event'];//获取事件类型
                      $this->setSession($ticket,$openId,$event);
                      return '谢谢关注';
                case 'text':
                    $openId = $message['FromUserName'];
                    return Redis::get($openId);
                    break;
                case 'image':
                    return session('userInfo');
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

    public function login(Request $request)
    {
        $app = app('wechat.official_account');

        $result = $app->qrcode->temporary('foo', 6 * 24 * 3600);

        $ticket = $result['ticket'];//获取二维码的key

        $url = $app->qrcode->url($result['ticket']);

        //保存key到redis中并且给定过期时间
        Redis::set($ticket,$ticket);
        Redis::expire($ticket,3600);

        return view('wechat.login',compact('url','ticket'));
    }

    public function oauth_callback()
    {
        $app = app('wechat.official_account');
        $user = $app->oauth->user();
        var_dump($user);
    }

    public function userInfo()
    {
        $app = app('wechat.official_account');

        $openId = Redis::get('openid');

        $user = $app->user->get($openId);

        dd($user);
    }

    public function setSession($ticket,$openId,$event)
    {
        $app = app('wechat.official_account');

        //判断如果是关注时间
        if ($event == 'subscribe'){
            $ticket = Redis::get($ticket);
            //获取二维码key
            if ($ticket){
                $userinfo = $app->user->get($openId);//获取用户信息
                Redis::set($ticket,json_encode($userinfo));
                Redis::expire($ticket,3600);
            }
        }

        Redis::set($openId,json_encode($userinfo));

        session::put(['userInfo' => 'linyiyuan']);
    }

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
}
