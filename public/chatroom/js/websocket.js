var ws;
var username;//用户名

$(function(){
  username = prompt("请输入您的名字", "")
  checkUsername(username);
  num  = Math.ceil(10*Math.random()); //随机生成一个数字
  avatar = '/chatroom/images/avatar/avatar'+num+'.jpeg' //随机生成一张头像
  link(); //调用websocket
})

//检查用户名
function checkUsername(username)
{
    if (username == '' || username == undefined) {
        username = prompt("请输入您的名字", "")
        checkUsername(username);

    }
}

//发送方法
function sendMsg(sendType){
    var msg = {};
    switch(sendType) {
      case 'open':
        msg.data = username;//声明发送的内容
        msg.sendType = sendType; //发送头像
        data = JSON.stringify(msg) //转换json 
        ws.send(data);
      break;

      case 'sendMessage' :
        msg.data = $('#content').val();//声明发送的内容
        msg.sendType = sendType
        msg.username = username; //发送人
        msg.avatar = avatar; //发送头像
        data = JSON.stringify(msg) //转换json 
        ws.send(data);
        $('#content').val('');
      break;
    }
    
}

 //websock
function link () {
      ws = new WebSocket("ws://localhost:9501");//连接服务器
      ws.onopen = function(event){
           sendMsg('open');
      };
      ws.onmessage = function (event) {
          var msg = JSON.parse(event.data);
          console.log(msg);
          switch(msg.type) {
            case 'open':
            var html ='<div class="col-xs-12 notice text-center">'+username+' 加入聊天室</div>';
            $('.chat-list').append(html);
            break;
            case 'message':
              if (msg.username == username) {
                var html = '<div class="col-xs-10 col-xs-offset-2 msg-item ">'
                              +'<div class="col-xs-1 no-padding pull-right">'
                              +'<div class="avatar">'
                              +'<img src="'+msg.avatar+'" width="50" height="50" class="img-circle">'
                              +'</div>'
                              +'</div>'

                              +'<div class="col-xs-11">'
                              +'<div class="col-xs-12">'
                              +'<div class="username pull-right">'+msg.username+'</div>'
                              +'<div>'
                              +'<div class="col-xs-12 no-padding">'
                              +'<div class="msg pull-right">'+msg.data+'</div>'
                              +'</div>'
                              +'</div>';
              }else{
                var html = '<div class="col-xs-10 msg-item ">'
                          +'<div class="col-xs-1 no-padding">'
                          +'<div class="avatar">'
                          +'<img src="'+msg.avatar+'" width="50" height="50" class="img-circle">'
                          +'</div>'
                          +'</div>'

                          +'<div class="col-xs-11 no-padding">'
                          +'<div class="col-xs-12">'
                          +'<div class="username">'+msg.username+'</div>'
                          +'</div>'
                          +'<div class="col-xs-12 no-padding">'
                          +'<div class="msg">'+msg.data+'</div>'
                          +'</div>'
                          +'</div>'
                          +'</div>';
              }
            $('.chat-list').append(html);
            $(".chat-list").scrollTop($(".chat-list")[0].scrollHeight)
            break;
          }
          
      }
      ws.onclose = function(event){alert("已经与服务器断开连接\r\n当前连接状态："+this.readyState);};

      ws.onerror = function(event){var html ='<div class="col-xs-12 notice text-center">WebSocket异常</div>';
      $('.chat-list').append(html);};
      
}