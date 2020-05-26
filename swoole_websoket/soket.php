<?php


// 存储在线客户的数组
$userFdList = [];

$server = new Swoole\WebSocket\Server("0.0.0.0", 9093);

$server->on('open', function ($server, $requset) use (&$userFdList) {
//$userFdList[$request->get['uid']] = $request->fd ; 用于给特定人发送情况
    $server->push($requset->fd, "欢迎来到同性交友群");
    $fd = $requset->fd;
    Swoole\Timer::after(1000, function () use ($server, $fd) {
        $server->push($fd, "欢迎来到同性交友群$fd");
    });
//    Swoole\Timer::tick(3000, function ($timer_id, $param1, $param2) {
//        echo "timer_id #$timer_id, after 3000ms.\n";
//        echo "param1 is $param1, param2 is $param2.\n";
//
//        Swoole\Timer::tick(14000, function ($timer_id) {
//            echo "timer_id #$timer_id, after 14000ms.\n";
//            Swoole\Timer::clearAll();
//        });
//
//    }, "A", "B");
});
$server->on('message', function ($server, $frame) use (&$userFdList) {
    $datas = json_decode($frame->data, true);
    //匿名群聊
    foreach ($server->connection_list() as $key => $val) {
        if ($val != $frame->fd)
            $server->push($val, "{$datas['msg']}");

    }


// 特定人发送
// if($datas['type'] == 1){
//     $server->push($userFdList[$datas['uid']], "系统：{$datas['msg']}");
// } else if( !isset($userFdList[$datas['to_uid']])) {
//     $server->push($userFdList[$datas['uid']], "系统：用户没上线");
// } else {
//     $server->push($userFdList[$datas['to_uid']], "别人(uid:{$datas['uid']})：{$datas['msg']}");
// }


// $server->push($frame->fd,{$frame->data});
});

$server->on('close', function ($server, $fd) use (&$userFdList) {
    echo "connection close: {$fd}\n";

    // 特定人发送
    // foreach ($userFdList as $key => $value) {
    //     if( $value == $fd ) unset($userFdList[$key]);
    // }
});


$server->start();


?>