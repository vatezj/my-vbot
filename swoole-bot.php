<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

date_default_timezone_set('Asia/Shanghai');

require __DIR__ . '/vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

//启动数据库连接池

$console = new Kcloze\Bot\Console($config);
$console->run();

use sethink\swooleOrm\Db;
use sethink\swooleOrm\MysqlPool;


class Demo
{
    protected $server;
    protected $MysqlPool;

    public function __construct()
    {
        $this->server = new Swoole\Http\Server("0.0.0.0", 9501);
        $this->server->set(array(
            'worker_num'    => 4,
            'max_request'   => 50000,
            'reload_async'  => true,
            'max_wait_time' => 30,
        ));

        $this->server->on('Start', function ($server) {});
        $this->server->on('ManagerStart', function ($server) {});
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('WorkerStop', function ($server, $worker_id) {});
        $this->server->on('open', function ($server, $request) {});
        $this->server->on('Request', array($this, 'onRequest'));
        $this->server->start();
    }

    public function onWorkerStart($server, $worker_id)
    {
        $config = [
            'host'      => '127.0.0.1', //服务器地址
            'port'      => 3306,    //端口
            'user'      => 'root',  //用户名
            'password'  => 'root',  //密码
            'charset'   => 'utf8',  //编码
            'database'  => 'test',  //数据库名
            'prefix'    => 'sethink_',  //表前缀
            'poolMin'   => 5, //空闲时，保存的最大链接，默认为5
            'poolMax'   => 1000,    //地址池最大连接数，默认1000
            'clearTime' => 60000, //清除空闲链接定时器，默认60秒，单位ms
            'clearAll'  => 300000,  //空闲多久清空所有连接，默认5分钟，单位ms
            'setDefer'  => true,     //设置是否返回结果,默认为true,
        ];
        $this->MysqlPool = new MysqlPool($config);
        unset($config);
           var_dump(  $this->MysqlPool);
        //执行定时器
        $this->MysqlPool->clearTimer($server);
    }

    public function onRequest($request, $response)
    {
        $rs = Db::init($this->MysqlPool)
            ->name('tt')
            ->select();
        var_dump($rs);
    }
}
//new Demo();
