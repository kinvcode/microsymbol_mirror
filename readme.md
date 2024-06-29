# 镜像服务



## Linux环境配置

### 安装supervisor

#### CentOs
```
yum install supervisor
```

#### Ubuntu
```
sudo apt-get install supervisor
```

#### 配置
##### 配置文件位置
系统 | 主配置文件位置 | 子配置文件目录
--- | --- | ---
CentOs | `/etc/supervisord.conf` | `/etc/supervisord.d`
Ubuntu | `/etc/supervisor/supervisord.conf` | `/etc/supervisor/conf.d`

##### 主配置文件说明
```
[unix_http_server]
file=/tmp/supervisor.sock   ;UNIX socket 文件，supervisorctl 会使用
;chmod=0700                 ;socket文件的mode，默认是0700
;chown=nobody:nogroup       ;socket文件的owner，格式：uid:gid
 
;[inet_http_server]         ;HTTP服务器，提供web管理界面
;port=127.0.0.1:9001        ;Web管理后台运行的IP和端口，如果开放到公网，需要注意安全性
;username=user              ;登录管理后台的用户名
;password=123               ;登录管理后台的密码
 
[supervisord]
logfile=/tmp/supervisord.log ;日志文件，默认是 $CWD/supervisord.log
logfile_maxbytes=50MB        ;日志文件大小，超出会rotate，默认 50MB，如果设成0，表示不限制大小
logfile_backups=10           ;日志文件保留备份数量默认10，设为0表示不备份
loglevel=info                ;日志级别，默认info，其它: debug,warn,trace
pidfile=/tmp/supervisord.pid ;pid 文件
nodaemon=false               ;是否在前台启动，默认是false，即以 daemon 的方式启动
minfds=1024                  ;可以打开的文件描述符的最小值，默认 1024
minprocs=200                 ;可以打开的进程数的最小值，默认 200
 
[supervisorctl]
serverurl=unix:///tmp/supervisor.sock ;通过UNIX socket连接supervisord，路径与unix_http_server部分的file一致
;serverurl=http://127.0.0.1:9001 ; 通过HTTP的方式连接supervisord
 
; [program:xx]是被管理的进程配置参数，xx是进程的名称
[program:xx]
command=/opt/apache-tomcat-8.0.35/bin/catalina.sh run  ; 程序启动命令
autostart=true       ; 在supervisord启动的时候也自动启动
startsecs=10         ; 启动10秒后没有异常退出，就表示进程正常启动了，默认为1秒
autorestart=true     ; 程序退出后自动重启,可选值：[unexpected,true,false]，默认为unexpected，表示进程意外杀死后才重启
startretries=3       ; 启动失败自动重试次数，默认是3
user=tomcat          ; 用哪个用户启动进程，默认是root
priority=999         ; 进程启动优先级，默认999，值小的优先启动
redirect_stderr=true ; 把stderr重定向到stdout，默认false
stdout_logfile_maxbytes=20MB  ; stdout 日志文件大小，默认50MB
stdout_logfile_backups = 20   ; stdout 日志文件备份数，默认是10
; stdout 日志文件，需要注意当指定目录不存在时无法正常启动，所以需要手动创建目录（supervisord 会自动创建日志文件）
stdout_logfile=/opt/apache-tomcat-8.0.35/logs/catalina.out
stopasgroup=false     ;默认为false,进程被杀死时，是否向这个进程组发送stop信号，包括子进程
killasgroup=false     ;默认为false，向进程组发送kill信号，包括子进程
 
;包含其它配置文件
[include]
files = relative/directory/*.ini    ;可以指定一个或多个以.ini结束的配置文件
```

##### 子配置文件
>子配置文件是以 `.ini` 为后缀的文件
###### 示例
```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /www/wwwroot/demo/artisan queue:work --queue=high,default
autostart=true
autorestart=true
user=root
numprocs=1
redirect_stderr=true
stdout_logfile=/www/wwwroot/demo/storage/logs/worker.log

```

#### 服务项

##### CentOs
```
// 查看运行状态
systemctl status supervisord

// 运行
systemctl start supervisord

// 停止运行
systemctl stop supervisord

// 加入开机启动项
systemctl enable supervisord

```

##### Ubuntu
```
// 查看运行状态
systemctl status supervisor

// 运行
systemctl start supervisor

// 停止运行
systemctl stop supervisor

// 加入开机启动项
systemctl enable supervisor
```

#### 常用命令
```
supervisorctl status        //查看所有进程的状态
supervisorctl stop all       //停止所有进程
supervisorctl start all      //启动所有进程
supervisorctl restart       //重启
supervisorctl update        //配置文件修改后使用该命令加载新的配置
supervisorctl reload        //重新启动配置中的所有程序
```


## NGINX配置
这一条一定要加
```
location / {
    try_files $uri $uri/ /index.php?$query_string; 
}
```

## MySQL配置
>MySQL无要求，但版本尽量使用MySQL5.7

## laravel项目配置

### 环境变量配置
1.复制环境变量文件
```shell
cp .env.example .env

php artisan key:generate
```
2.配置环境变量
```
打开.env文件并修改以下字段

APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mirror_demo
DB_USERNAME=mirror_demo
DB_PASSWORD=mirror_demo

QUEUE_CONNECTION=database
```

| 字段名       | 描述                       
|-----------|--------------------------
| APP_NAME  | 应用名称，随便写                 
| APP_ENV   | 测试：local；发布：prod         
| APP_DEBUG | 是否开启调试.测试:true; 发布:false |
| APP_URL   | 应用域名，域名或ipv4均可
| DB_HOST   | MySQL IP
| DB_PORT   | MySQL端口
| DB_DATABASE | 数据库名
| DB_USERNAME | 数据库用户名
| DB_PASSWORD | 数据库密码
| QUEUE_CONNECTION | 队列驱动，database就是MySQL

3.安装依赖包
>composer事先安装好
```
composer install
```

4.队列管理
>安装supervisor，配置队列任务常驻运行


## 项目说明

## 普通请求
与大多数镜像站一致，直接访问文件URL即可下载

### 多任务请求
>作为项目中唯一一个API，也是一个特殊的API，该API目的仅为批量增加队列任务（下载）

### 批量任务请求示例
* api：https://www.mirror.com
* 请求方法：POST
* 请求体
```json
{
    "MultipleTasks": [
        "rspndr.pdb/8494A12933F69C9A33F285FF3B63F5161/rspndr.pdb",
        "rspndr.pdb/B890F401FFEEE8D9CE9B96ECBDB055771/rspndr.pdb"
    ]
}
```
