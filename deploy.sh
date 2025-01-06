#!/bin/bash
# 管理员
if [ "$(id -u)" -ne 0 ]; then
    echo "请切换root（sudo su）"
    exit 0
fi
# 检测pnpm
if ! command -v pnpm >/dev/null 2>&1; then
    echo "pnpm 未安装"
    exit 0
fi
# 检测php
if [ "$(php -v | grep -Eo 'PHP [0-9]+\.[0-9]+')" != "PHP 8.1" ];then
  if ! command -v php81 >/dev/null 2>&1; then
      if ! command -v php8.1 >/dev/null 2>&1; then
        echo "php8.1 未安装"
        exit 0
      fi
  fi
fi

# 检测composer
if ! command -v composer >/dev/null 2>&1; then
    echo "composer 未安装"
    exit 0
fi
# 配置文件
if [ ! -e "serve/.env" ]; then
	cp serve/.env.example serve/.env
	echo "请完成serve/.env数据库等其他相关配置"
	exit 0
fi
if [ -z "$(sed -n 's/DB_PASSWORD=//p' serve/.env)" ] || grep -q "DB_DATABASE=laravel" serve/.env;then
	echo "请配置env数据库账号！"
	exit 0
fi
if grep -q "https://您的域名" admin/.env; then
    echo "请配置后台接口域名！(admin/.env)"
    exit 0
fi

# 编译后台
echo "cd admin"
cd admin
if [ ! -e "install.lock" ]; then
  if ! pnpm install; then
      echo "pnpm 安装失败"
      exit 0
  fi
  touch install.lock
fi

echo "编译后台"
pnpm build

cd ../

# API初始
echo "cd serve"
cd serve

if [ ! -e "install.lock" ]; then
   if ! composer install;then
       echo "接口安装失败"
       exit 0
   fi
   echo "生成API KEY"
   php artisan key:generate
   echo "初始数据库"
   php artisan migrate
   echo "初始管理员账号"
   php artisan app:system-init
   if [ ! -d "public/storage" ]; then
      php artisan storage:link
   fi
   touch install.lock
fi

cd ../
nginx_user=$(ps aux | grep nginx | grep -v grep | sed -n '2p' | grep -oE '^[^ ]+')
chown -R "$nginx_user" admin
chown -R "$nginx_user" serve
echo "完成！默认账号admin 密码admin123"
