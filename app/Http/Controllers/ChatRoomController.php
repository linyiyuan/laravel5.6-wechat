<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ChatRoomController extends Controller
{
    public function index()
    {
    	return view('chatroom');
    }

    /**
     * @Author    linyiyuan
     * @DateTime  2018-09-10
     * @聊天室代码逻辑
     */
    public static function server($port='9501')
    {
    	Redis::set('chatroom:port',$port);
    	$server = new \swoole_websocket_server('0.0.0.0', $port);
	     
		$server->on('open', function (\swoole_websocket_server $server, $request) {
		    echo "server: handshake success with fd{$request->fd}\n";
		});
		 
		$server->on('message', function (\swoole_websocket_server $server, $frame) {
		    foreach($server->connections as $key => $fd) {
		        $user_message = $frame->data;
		        $user_message = json_decode($user_message,true);
		        if ($user_message['sendType'] == 'open') {
		        	$sendType = 'open';
		        }else{
		        	$sendType = 'sendMessage';
		        }
				$user_message = json_encode($user_message,JSON_UNESCAPED_UNICODE);
		        Self::pushMessage($server, $user_message,$sendType);
		    }

		});
		 
		$server->on('close', function ($ser, $fd) {
			Redis::del('chatroom:port');
		    echo "client {$fd} closed\n";
		});
		 
		$server->start();
    }

    /**
     * @Author    linyiyuan
     * @DateTime  2018-09-10
     * @发送信息
     */
    public static function pushMessage(\swoole_websocket_server $server,$message,$sendType = 'sendMessage')
    {
    	$message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        foreach($server->connections as $key => $fd) {
		    $server->push($fd, json_encode([
	                'type' => $sendType,
	                'message' => $message,
	                'datetime' => $datetime,
           		 ])
       		 );  
		}
        
    }
}
