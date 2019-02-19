# 微信服务器系统

- v1.0 林益远 2018.10.26 修订

## 1 前言
### 1.1 项目说明
该项目使用laravel5.6+esaywechat进行开发，主要针对微信登录验证，支付接口等

### 1.2 注意事项
该项目仅供学习测试参考，切勿使用于商业用途，主要的业务有以下:

- 网页授权登录
- 支付接口（未开发）


## 2 如何部署
### 2.1 开发说明
- 开发框架：`Laravel 5.6`
- PHP版本：`PHP 7.1`
- 扩展：`Redis`、`Swoole` `esaywechat`

### 2.2 安装
#### 2.2.1 基本要求
- 服务器要求：
	- PHP >= 7.1.3
	- OpenSSL PHP扩展
	- PDO PHP扩展，注意需要php_mysql
	- Mbstring PHP扩展
	- Tokenizer PHP扩展
	- XML PHP扩展
	- Swoole PHP扩展
	- Redis PHP扩展

<br>

#### 2.2.2 安装步骤
以下为本项目git仓库地址

	git@github.com:linyiyuan/laravel5.6-wechat.git
	
<br>
**第1步：克隆代码**

	git clone git@github.com:linyiyuan/laravel5.6-wechat.git
<br>
**第2步：安装composer包**
	
	composer install
	

<br>
**第3步：配置文件**

1、在项目中找到`.env.example`文件，该文件作为项目的全局配置文件，在部署时需要复制成`.env`，执行以下命令

	cp -f .env.example ./.env
2、根据.env文件修改各配置项，如果.env文件中没有存在key值则运行命令：
	
	  php artisan key:generate

<br>
**第4步：初始化数据库**

在根路径上执行以下命令来实现初始化数据库结构。注意执行该命令前请检查项目是否已依赖`doctrine/dbal`

	php artisan migrate

至此基本以完成，可以在浏览器中访问域名，如出现`larvel`字符串页面则说明部署完成，后续请根据各需求点作测试

<br>	

#### 2.2.2 安装步骤
	
	server {
    listen 80;
    server_name web.wechat.com;
    root 项目路径;
    index index.php index.html index.htm;
    
		 location / {
		        #add_header 'Access-Control-Allow-Origin' 'http://manager2.web';
		         if (!-e $request_filename){
		            rewrite  ^/(.*)$  /index.php?s=$1  last;
		        }
		   }
		   location /obs/ {
		        index obs.php;
		        rewrite ^/obs/(.*)$ /obs.php?s=/obs/$1 last;
		   }
		   location /guild/ {
		       index guild.php;
		       rewrite ^/guild/(.*)$ /guild.php?s=/guild/$1 last;
		   }


		    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
		    location ~ \.php$ {
		        #include snippets/fastcgi-php.conf;

			root 项目路径;
		        # With php7.0-cgi alone:
		        fastcgi_pass 127.0.0.1:9000;
			proxy_read_timeout 300;
		        fastcgi_read_timeout 600;
		        ## With php7.0-fpm:
		        #fastcgi_pass unix:/run/php/php7.0-fpm.sock;
			fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
			include        fastcgi_params;
		    }

		    location ~ /\.ht {
		        deny all;
		    }
		}
