swoole frame 

其实也称不上什么frame，只是针对于swoole_server做了简单的分发处理，并基于code做了简单的路由分发。

运行方式
php socket.php

Usage: php socket.php (start | stop | reload | restart)


你可以使用一下命令参数进行服务器的管理
php socket start		#启动
php socket stop			#停止
php socket reload		#软重启 服务不会暂停
php socket restart		#硬重启
