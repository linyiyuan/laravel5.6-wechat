<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//测试路由
Route::get('test','TestController@test');

//聊天室
//Route::get('/','ChatRoomController@index');

/*
|--------------------------------------------------------------------------
| WeChat Route
|--------------------------------------------------------------------------
|
|
*/
Route::group(['prefix' => 'wechat','namespace' => 'WeChat'], function(){
    //微信服务端
    Route::any('/','InitController@init');
    //微信二维码
    Route::any('/login','InitController@login');
    //检查微信是否登录
    Route::post('/session','InitController@checkSession');
//    Route::any('/wechat/userinfo','WeChat\WeChatController@userInfo');
//    Route::any('/wechat/wechatlogin','WeChat\WeChatLoginController@index');
});

