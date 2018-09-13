培训课程
====
mini_front 前端小程序

Laravel 做后端管理和API

概要说明
---
小程序培训报名+后端管理

#部署安装

小程序
--
* 开发者工具打开mini_front
* 修改config.js文件的host,接口地址
* 小程序后台配置消息模板

后台部署
--
* 导入数据库train.sql
* 修改.env数据库配置
* 修改小程序配置和商户支付配置,/config/wechat.php mini项

参考
---
微信sdk

https://github.com/overtrue/laravel-wechat 

后台管理系统框架

https://github.com/DukeAnn/Laradmin/blob/master

https://lipis.github.io/bootstrap-sweetalert/

https://github.com/andersao/l5-repository

##性能优化
    1、配置信息缓存
    php artisan config:cache //生成
    php artisan config:clear //取消
    2、路由缓存
    php artisan route:cache //生成
    php artisan route:clear //取消
    3、类映射缓存
    php artisan optimize
    php artisan clear-compiled
    4、自动加载
    composer dumpautoload -o
    5、关闭应用debug app.debug=false


