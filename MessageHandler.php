<?php

namespace Hanson\MyVbot;

use Hanson\MyVbot\Handlers\Contact\ColleagueGroup;
use Hanson\MyVbot\Handlers\Contact\ExperienceGroup;
use Hanson\MyVbot\Handlers\Contact\FeedbackGroup;
use Hanson\MyVbot\Handlers\Contact\Hanson;
use Hanson\MyVbot\Handlers\Type\RecallType;
use Hanson\MyVbot\Handlers\Type\TextType;
use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;

use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;



class MessageHandler
{
    public static function messageHandler(Collection $message)
    {
        /** @var Friends $friends */
        $friends = vbot('friends');

        /** @var Members $members */
        $members = vbot('members');

        /** @var Groups $groups */
        $groups = vbot('groups');

        Hanson::messageHandler($message, $friends, $groups);
        ColleagueGroup::messageHandler($message, $friends, $groups);
        FeedbackGroup::messageHandler($message, $friends, $groups);
        ExperienceGroup::messageHandler($message, $friends, $groups);

        TextType::messageHandler($message, $friends, $groups);
        RecallType::messageHandler($message);
        if ($message['type'] === 'new_friend') {
            $friends->approve($message);
            Text::send($message['from']['UserName'], '客官，等你很久了！感谢跟我交朋友，现在传您一套武功秘籍http://sina.lt/gaAn');
            Emoticon::sendRandom($message['from']['UserName']);
        }

        // @todo

        if($message['fromType'] ===  'Friend' && $message['type'] === 'text'){
            $datas['password'] = $message['content'];
            $data = explode("￥", $message['content']);
            if (isset($data[1])) {
                Text::send($message['from']['UserName'], '正在查询中......');
            }
            $result = self::deelText( $message['content'],$message['username']);
            Text::send($message['from']['UserName'], $result);
        }

        if($message['fromType'] ===  'official' && $message['type'] === 'text'){

        }

        if($message['fromType'] ===  'Group' && $message['type'] === 'text'){
            $datas['password'] = $message['content'];
            $data = explode("￥", $message['content']);
            if (isset($data[1])) {
                Text::send($message['from']['UserName'], '正在查询中......');
                $result = self::deelText( $message['content'],$message['username']);
                Text::send($message['from']['UserName'], $result);
            }
            if($message['content'] === "帮助")
            {
                $result = self::deelText( "帮助",$message['username']);
                Text::send($message['from']['UserName'], $result);
            }
        }


        if ($message['type'] === 'request_friend') {
            vbot('console')->log('收到好友申请:'.$message['info']['Content'].$message['avatar']);
            if (in_array($message['info']['Content'], ['echo', 'print_r', 'var_dump', 'print'])) {
                $friends->approve($message);
            }
        }
        if ($message['type'] === 'group_change' && $message['action'] === "ADD") {

            Text::send($message['from']['UserName'], '欢迎新人 '.$message['invited'].PHP_EOL.'邀请人：'.$message['inviter']);
            $result = "@{$message['invited']}".PHP_EOL."点击进入首页  http://www.taoquan.ink \n" .
                "点击进入淘口令  http://www.taoquan.ink/pages/password/password \n" .
                "点击进入分类   http://www.taoquan.ink/pages/category/wkiwi-classify \n" .
                "点击进入搜索  http://www.taoquan.ink/pages/search/search \n" .
                "点击进入9.9包邮   http://www.taoquan.ink/pages/lists/lists_nine \n" .
                "点击进入说明  https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng \n" .
                "点击进入达人说   http://www.taoquan.ink/pages/talent/talent \n";
            Text::send($message['from']['UserName'], $result);
        }

        if ($message['type'] === 'request_friend') {
                vbot('console')->log('收到好友申请:'.$message['info']['Content'].$message['avatar']);
                if (in_array($message['info']['Content'], ['echo', 'print_r', 'var_dump', 'print'])) {
                    $friends->approve($message);
                }
        }
    }

    public static  function deelText($content,$messageId = false)
    {
        switch ($content) {
            case '首页':
                $text = "{$content}:\n
                        点击进入  http://www.taoquan.ink";
                break;
            case '淘口令':
                $text = "{$content}:\n
                        点击进入  http://www.taoquan.ink/pages/password/password";
                break;
            case '分类':
                $text = "{$content}:\n
                        点击进入  http://www.taoquan.ink/pages/category/wkiwi-classify";

                break;
            case '搜索':
                $text = "{$content}:\n
                        点击进入 http://www.taoquan.ink/pages/search/search";

                break;
            case '9.9包邮':
                $text = "{$content}:\n
                        点击进入 http://www.taoquan.ink/pages/lists/lists_nine";
                break;
            case '达人说':
                $text = "{$content}:\n
                        点击进入  http://www.taoquan.ink/pages/talent/talent";

                break;
            case '帮助':
                $text = "点击进入首页  http://www.taoquan.ink \n" .
                        "点击进入淘口令  http://www.taoquan.ink/pages/password/password \n" .
                        "点击进入分类   http://www.taoquan.ink/pages/category/wkiwi-classify \n" .
                        "点击进入搜索  http://www.taoquan.ink/pages/search/search \n" .
                        "点击进入9.9包邮   http://www.taoquan.ink/pages/lists/lists_nine \n" .
                        "点击进入说明  https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng \n" .
                        "点击进入达人说   http://www.taoquan.ink/pages/talent/talent \n";
                break;
            default:
                $data = explode("￥", $content);
                if (isset($data[1])) {
                    $datas['password'] = $content;
                    $text =  self::http_post('http://api.taoquan.ink/api/product/change_password', $datas);;
                } else {
                    $text = "点击进入首页  http://www.taoquan.ink \n" .
                            "点击进入淘口令  http://www.taoquan.ink/pages/password/password \n" .
                            "点击进入分类   http://www.taoquan.ink/pages/category/wkiwi-classify \n" .
                            "点击进入搜索  http://www.taoquan.ink/pages/search/search \n" .
                            "点击进入9.9包邮   http://www.taoquan.ink/pages/lists/lists_nine \n" .
                            "点击进入说明  https://mp.weixin.qq.com/s/J4ZOMnoaRSA5Y1baWuU-Ng \n" .
                            "点击进入达人说   http://www.taoquan.ink/pages/talent/talent \n";
                }
                break;
        }
        return $text;
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

    public static function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }



}
