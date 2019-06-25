<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

use Hanson\Vbot\Message\Text;
use Kcloze\Bot\Api\Baidu;
use Kcloze\Bot\Api\Taoke;
use Kcloze\Bot\Api\Tuling;

class Reply
{
    private $message;
    private $options;

    public function __construct($message, $options)
    {
        $this->message =$message;
        $this->options =$options;
    }

    public function send()
    {
        $type=$this->message['type'];
        switch ($type) {
            case 'text':
                //@我或者好友发消息都自动回复
                if ($this->message['fromType'] == 'Friend' || $this->message['fromType'] ===  'Group' && true == $this->message['isAt']) {
                    $data = explode("￥", $this->message['content']);
                    if (isset($data[1])) {
                        $this->findPassword($this->message);
                    }else
                    {
                        if($this->message['fromType'] ===  'Group' && true == $this->message['isAt']){
                            $this->reboot($this->message);
                        }
                        if($this->message['fromType'] == 'Friend'){
                            $this->reboot($this->message);
                        }

                    }
                }else{

                }


                break;
            case 'voice':
                // code...
                break;
            case 'image':
                // code...
                break;
            case 'emoticon':
                // code...
                break;
            case 'red_packet':
                // code...
                break;
            case 'new_friend':
                echo '新增好友' . $this->message['from']['UserName'] . '请求' . PHP_EOL;
                Text::send($this->message['from']['UserName'], '客官，等你很久了！感谢跟 oop 交朋友，我是 kcloze 的贴身秘书，当你累了困惑了，可以随时呼叫我！' . PHP_EOL . '高山流水遇知音，知音不在谁堪听？焦尾声断斜阳里，寻遍人间已无');
                break;
            case 'request_friend':
                echo '新增好友' . $this->message['from']['UserName'] . '请求，自动通过' . PHP_EOL;
                $friends = vbot('friends');
                $friends->approve($this->message);
                break;
            case 'group_change':
                Text::send($this->message['from']['UserName'], '欢迎新人 ' . $this->message['invited'] . PHP_EOL . '邀请人：' . $this->message['inviter']);
                break;
            default:
                // code...
                break;

        }
    }

    private function findPassword($message){
        Text::send($message['from']['UserName'], '正在查询中......');
        $tuling =new Taoke($this->options);
        $return =$tuling->search($message['content']);
        Text::send($message['from']['UserName'], $return);
    }

    private function reboot($message){
        $tuling =new Taoke($this->options);
        $return =$tuling->reboot($message['content']);
        Text::send($message['from']['UserName'], $return);
        $back = $return;
//        $data = json_decode($return,true);
//        vbot('console')->log($return.'1111');
        $return = substr($return,1);
//        vbot('console')->log($this->is_json($return));
        if(!$this->is_not_json($return)){
            vbot('console')->log($return);
            $data = json_decode($return,true);
            $return = $this->jieqian($data);
            Text::send($message['from']['UserName'], $return);
        }else
        {

            Text::send($message['from']['UserName'], $return);
        }
    }

    private function jieqian($arr){
        $str = '';
        if(isset($arr['type'])){

            $str = '签号:  '.$arr['number1'].PHP_EOL;
            $str .= '签号:  '.$arr['number2'].PHP_EOL;
            if(isset($arr['haohua'])){
                $str .= '好与坏: '.$arr['haohua'].PHP_EOL;
            }
            if(isset($arr['qianyu'])){
                $str .= '签语:  '.$arr['qianyu'].PHP_EOL;
            }
            if(isset($arr['zhushi'])){
                $str .= '注释:  '.$arr['zhushi'].PHP_EOL;
            }
            if(isset($arr['baihua'])){
                $str .= '白话浅释:  '.$arr['baihua'].PHP_EOL;
            }
            if(isset($arr['jieshuo'])){
                $str .= '解说:  '.$arr['qianyu'].PHP_EOL;
            }
            if(isset($arr['jieguo'])){
                $str .= '抽到此签:  '.$arr['jieguo'].PHP_EOL;
            }
            if(isset($arr['hunyin'])){
                $str .= '婚姻:  '.$arr['hunyin'].PHP_EOL;
            }
            if(isset($arr['shiye'])){
                $str .= '事业:  '.$arr['shiye'].PHP_EOL;
            }
            if(isset($arr['gongming'])){
                $str .= '功名:  '.$arr['gongming'].PHP_EOL;
            }
            if(isset($arr['shiwu'])){
                $str .= '失物:  '.$arr['shiwu'].PHP_EOL;
            }
            if(isset($arr['cwyj'])){
                $str .= '出外移居:  '.$arr['cwyj'].PHP_EOL;
            }
            if(isset($arr['liujia'])){
                $str .= '六甲:  '.$arr['liujia'].PHP_EOL;
            }
            if(isset($arr['qiucai'])){
                $str .= '求财:  '.$arr['qiucai'].PHP_EOL;
            }
            if(isset($arr['jiaoyi'])){
                $str .= '交易:  '.$arr['jiaoyi'].PHP_EOL;
            }
            if(isset($arr['jibin'])){
                $str .= '疾病:  '.$arr['jibin'].PHP_EOL;
            }
            if(isset($arr['susong'])){
                $str .= '诉讼:'.$arr['susong'].PHP_EOL;
            }
            if(isset($arr['yuntu'])){
                $str .= '运途:  '.$arr['yuntu'].PHP_EOL;
            }
//            if(isset($arr['moushi'])){
//                $str .= '某事:'.PHP_EOL.$arr['moushi'].PHP_EOL;
//            }
            if(isset($arr['moushi'])){
                $str .= '某事:  '.$arr['moushi'].PHP_EOL;
            }
            if(isset($arr['hhzsy'])){
                $str .= '合伙做生意:  '.$arr['hhzsy'].PHP_EOL;
            }
//            $str .= '诗意解签:  '.$arr['shiyi'].PHP_EOL;
            if(isset($arr['shiyi'])){
                $str .= '诗意解签:  '.$arr['shiyi'].PHP_EOL;
            }
            $str .= '白话解签:  '.$arr['jieqian'].PHP_EOL;
            $str .= '灵签类型:  '.$arr['type'].PHP_EOL;

        }else
        {
            $str = $arr['title'].PHP_EOL.$arr['content'];
        }
        return $str;
    }


    private  function is_not_json($data) {
        return is_null(json_decode($data));
    }

}