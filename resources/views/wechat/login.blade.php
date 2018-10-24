<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
        <div class="erweima" style="display: none">
            <img src="{{ $url }}" alt="">
            <p >{{ $ticket }}</p>
        </div>

        <div class="info" style="display: none">
            <p>登录成功</p>
            <p class="nickname"></p>
            <p class="city"></p>
            <p class="headimgurl"></p>
            <p></p>
        </div>
</body>
<script src="{{ asset('js/jquery.js') }}"></script>
<script src="//cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

<script>
    userinfo = $.cookie('userinfo');
    if (!userinfo){
        $('.erweima').css('display','block');
        setInterval(function(){
            getMsgNum();
        },1000);
    }else{
        data = JSON.parse(userinfo);
        $('.erweima').css('display','none');
        $('.info').css('display','block');
        $('.nickname').html(data['nickname']);
        $('.city').html(data['city']);
        $('.headimgurl').html("<img src="+ data['headimgurl'] + ">");
        console.log(data);
    }

    function getMsgNum(){
        ticket = "{{ $ticket }}";
        $.ajax({
            url:'/wechat/session',
            type:'post',
            dataType:'json',
            data:{ticket:ticket},
            success:function(data){
                if (data.code == 200){
                    $.cookie('userinfo', data.data);
                    location.href="/wechat/login";
                }
            }
        });
    }
</script>
</html>