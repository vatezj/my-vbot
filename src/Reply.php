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

    protected static $left = '[emoji:';
    protected static $right = ']';
    protected static $pattern = '/\[emoji\:(.*?)\]/is';

    public function __construct($message, $options)
    {
        $this->message = $message;
        $this->options = $options;
    }

    private function checkStr($target, $str)
    {
        $tmpArr = explode($str, $target);
        //print_r($tmpArr);
        if (count($tmpArr) > 1) return true;
        else return false;
    }

    private function delTexts($string)
    {
        //₳  ₴  $ ¢  € ₤
        if ($this->checkStr($this->message['content'], '₳')) {
            $ext = '₳';
        } else if ($this->checkStr($this->message['content'], '₴')) {
            $ext = '₴';
        } else if ($this->checkStr($this->message['content'], '€')) {
            $ext = '€';
        } else if ($this->checkStr($this->message['content'], '¢')) {
            $ext = '¢';
        } else if ($this->checkStr($this->message['content'], '￥')) {
            $ext = '￥';
        } else if ($this->checkStr($this->message['content'], '$')) {
            $ext = '$';
        } else if ($this->checkStr($this->message['content'], '₤')) {
            $ext = '₤';
        } else {
            return false;
        }

        vbot('console')->log($ext);
        return $ext;

    }

    public function send()
    {
        $type = $this->message['type'];
//        vbot('console')->log(json_encode($this->message));
        $friends = vbot('friends');

//        vbot('console')->log(json_encode($friends));

        switch ($type) {
            case 'text':
                //@我或者好友发消息都自动回复
                if ($this->message['content'] == '帮助' || $this->message['content'] == '说明') {
                    Text::send($this->message['from']['UserName'], '我可以查天气，手机归属地，qq状态{@qq+QQ号}，算卦{发送观音灵签、月老灵签、财神爷灵签}，以及讲笑话' . PHP_EOL . '最重要的，我可以帮助您找淘宝内部优惠券{点击链接看教程 https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng}');

                } else {
                    if ($this->message['fromType'] == 'Friend' || $this->message['fromType'] === 'Group' || true == $this->message['isAt']) {
                        $ext = $this->delTexts($this->message['content']);
                        if ($ext) {
                            $data = explode($ext, $this->message['content']);
                            //₳  ₴  $ ¢  €
                            if (isset($data[1])) {
                                $this->findPassword($this->message);
                            } else {
                                $pattern = '/^\d{18}$/';
                                $text = $this->get('text');
                                if (!preg_match($pattern,  $text, $matches)) {
                                    if ($this->message['fromType'] === 'Group' && true == $this->message['isAt']) {
                                        $this->reboot($this->message);
                                    }
                                    if ($this->message['fromType'] == 'Friend') {
                                        $this->reboot($this->message);
                                    }
                                }else{
                                    if($this->message['from']['RemarkName'] == "通知订单"){
                                        $postInfo['order_id'] = $this->message['content'];
                                        $text =  self::http_post('http://api.taoquan.ink/api/rebate/updata_order', $postInfo);
                                        Text::send($this->message['from']['UserName'], $text);
                                    }else{
                                        $info = "";
                                        foreach ($friends as $k=>$v){if($v['RemarkName'] == '通知订单'){$info = $v;break;}}
                                        Text::send($info['UserName'], $this->message['from']['NickName']);
                                        $postInfo['order_id'] = $this->message['content'];
                                        $postInfo['nickname'] = self::en($this->message['from']['NickName']);
                                        $text =  self::http_post('http://api.taoquan.ink/api/rebate/insert_order', $postInfo);
                                        $text = json_decode($text,true);
                                        Text::send($this->message['from']['UserName'], $text['data']);
                                    }
                                }
                            }
                        } else {
                            $pattern = '/^\d{18}$/';
                            if (!preg_match($pattern,  $this->message['content'], $matches)) {
                                if ($this->message['fromType'] === 'Group' && true == $this->message['isAt']) {
                                    $this->reboot($this->message);
                                }
                                if ($this->message['fromType'] == 'Friend') {
                                    $this->reboot($this->message);
                                }
                            }else
                            {
                              if($this->message['from']['RemarkName'] == "通知订单"){
                                  $postInfo['order_id'] = $this->message['content'];
                                  $text =  self::http_post('http://api.taoquan.ink/api/rebate/updata_order', $postInfo);
                                  Text::send($this->message['from']['UserName'], $text);
                              }else{
                                  $info = "";
                                  foreach ($friends as $k=>$v){if($v['RemarkName'] == '通知订单'){$info = $v;break;}}
                                  Text::send($info['UserName'], $this->message['from']['NickName']);
                                  $postInfo['order_id'] = $this->message['content'];
                                  $postInfo['nickname'] = self::en($this->message['from']['NickName']);
                                  $text =  self::http_post('http://api.taoquan.ink/api/rebate/insert_order', $postInfo);
                                  $text = json_decode($text,true);
                                  Text::send($this->message['from']['UserName'], $text['data']);
                              }

                            }

                        }

                        //₳  ₴  $ ¢  €

                    }
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
                Text::send($this->message['from']['UserName'], '客官，等你很久了！我是您的的贴身秘书，当你累了困惑了，可以随时呼叫我！' . PHP_EOL .
                    '我可以查天气，手机归属地，qq状态{@qq+QQ号}，算卦{发送观音灵签、月老灵签、财神爷灵签}，以及讲笑话' . PHP_EOL . '最重要的，我可以帮助您找淘宝内部优惠券{点击链接看教程 https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng}');
                break;
            case 'request_friend':
                echo '新增好友' . $this->message['from']['UserName'] . '请求，自动通过' . PHP_EOL;
                $friends = vbot('friends');
                $friends->approve($this->message);
                Text::send($this->message['from']['UserName'], '客官，等你很久了！我是您的的贴身秘书，当你累了困惑了，可以随时呼叫我！' . PHP_EOL .
                    '我可以查天气，手机归属地，qq状态{@qq+QQ号}，算卦{发送观音灵签、月老灵签、财神爷灵签}，以及讲笑话' . PHP_EOL . '最重要的，我可以帮助您找淘宝内部优惠券{点击链接看教程 https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng}');
                break;
            case 'group_change':
                Text::send($this->message['from']['UserName'], '欢迎新人 ' . $this->message['invited'] . PHP_EOL . '邀请人：' . $this->message['inviter']);
                break;
            default:
                // code...
                break;

        }
    }

    public static function http_post($url,$param,$post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    private function findPassword($message)
    {
        Text::send($message['from']['UserName'], '正在查询中......');
        $tuling = new Taoke($this->options);
        $return = $tuling->search($message['content']);
        Text::send($message['from']['UserName'], $return);
    }

    private function reboot($message)
    {
        $tuling = new Taoke($this->options);
        if (isset($message['isAt']) && $message['isAt'] == true) {
            $message['content'] = $message['pure'];
        }
        $return = $tuling->reboot($message['content']);
        Text::send($message['from']['UserName'], $return);
        $back = $return;
//        $data = json_decode($return,true);
//        vbot('console')->log($return.'1111');
        $return = substr($return, 1);
//        vbot('console')->log($this->is_json($return));
        if (!$this->is_not_json($return)) {
//            vbot('console')->log($return);
            $data = json_decode($return, true);
            $return = $this->jieqian($data);
            Text::send($message['from']['UserName'], $return);
        } else {

            Text::send($message['from']['UserName'], $return);
        }
    }

    private function jieqian($arr)
    {
        $str = '';
        if (isset($arr['type'])) {

            $str = '签号:  ' . $arr['number1'] . PHP_EOL;
            $str .= '签号:  ' . $arr['number2'] . PHP_EOL;
            if (isset($arr['haohua'])) {
                $str .= '好与坏: ' . $arr['haohua'] . PHP_EOL;
            }
            if (isset($arr['qianyu'])) {
                $str .= '签语:  ' . $arr['qianyu'] . PHP_EOL;
            }
            if (isset($arr['zhushi'])) {
                $str .= '注释:  ' . $arr['zhushi'] . PHP_EOL;
            }
            if (isset($arr['baihua'])) {
                $str .= '白话浅释:  ' . $arr['baihua'] . PHP_EOL;
            }
            if (isset($arr['jieshuo'])) {
                $str .= '解说:  ' . $arr['qianyu'] . PHP_EOL;
            }
            if (isset($arr['jieguo'])) {
                $str .= '抽到此签:  ' . $arr['jieguo'] . PHP_EOL;
            }
            if (isset($arr['hunyin'])) {
                $str .= '婚姻:  ' . $arr['hunyin'] . PHP_EOL;
            }
            if (isset($arr['shiye'])) {
                $str .= '事业:  ' . $arr['shiye'] . PHP_EOL;
            }
            if (isset($arr['gongming'])) {
                $str .= '功名:  ' . $arr['gongming'] . PHP_EOL;
            }
            if (isset($arr['shiwu'])) {
                $str .= '失物:  ' . $arr['shiwu'] . PHP_EOL;
            }
            if (isset($arr['cwyj'])) {
                $str .= '出外移居:  ' . $arr['cwyj'] . PHP_EOL;
            }
            if (isset($arr['liujia'])) {
                $str .= '六甲:  ' . $arr['liujia'] . PHP_EOL;
            }
            if (isset($arr['qiucai'])) {
                $str .= '求财:  ' . $arr['qiucai'] . PHP_EOL;
            }
            if (isset($arr['jiaoyi'])) {
                $str .= '交易:  ' . $arr['jiaoyi'] . PHP_EOL;
            }
            if (isset($arr['jibin'])) {
                $str .= '疾病:  ' . $arr['jibin'] . PHP_EOL;
            }
            if (isset($arr['susong'])) {
                $str .= '诉讼:' . $arr['susong'] . PHP_EOL;
            }
            if (isset($arr['yuntu'])) {
                $str .= '运途:  ' . $arr['yuntu'] . PHP_EOL;
            }
//            if(isset($arr['moushi'])){
//                $str .= '某事:'.PHP_EOL.$arr['moushi'].PHP_EOL;
//            }
            if (isset($arr['moushi'])) {
                $str .= '某事:  ' . $arr['moushi'] . PHP_EOL;
            }
            if (isset($arr['hhzsy'])) {
                $str .= '合伙做生意:  ' . $arr['hhzsy'] . PHP_EOL;
            }
//            $str .= '诗意解签:  '.$arr['shiyi'].PHP_EOL;
            if (isset($arr['shiyi'])) {
                $str .= '诗意解签:  ' . $arr['shiyi'] . PHP_EOL;
            }
            $str .= '白话解签:  ' . $arr['jieqian'] . PHP_EOL;
            $str .= '灵签类型:  ' . $arr['type'] . PHP_EOL;

        } else {
            $str = $arr['title'] . PHP_EOL . $arr['content'];
        }
        return $str;
    }




    /**
     * 将字符串格式化为emoji代码
     *
     * @param string|array $data 需要被格式化的数据
     * @return string|array 格式化后的数据
     */
    public static function en($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = static::en($value);
            }
        } elseif (is_string($data)) {
            $left = static::$left;
            $right = static::$right;
            $data = preg_replace_callback('/[\xf0-\xf7].{3}/', function ($data) use ($left, $right) {
                $data = array_pop($data);
                $data = base64_encode($data);
                $data = sprintf('%s%s%s', $left, $data, $right);
                return $data;
            }, $data);
        }
        return $data;
    }

    /**
     * 反格式化数据
     *
     * @param string|array $data 需要被反格式化的数据
     * @return string|array 反格式化后为emoji原型字符串
     */
    public static function de($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = static::de($value);
            }
        } elseif (is_string($data)) {
            $data = preg_replace_callback(static::$pattern, function ($data) {
                $data = $data[1];
                $data = base64_decode($data);
                return $data;
            }, $data);
        }
        return $data;
    }


    private function is_not_json($data)
    {
        return is_null(json_decode($data));
    }

}
