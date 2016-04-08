<?php

/*
 * Copyright 2015 master.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

defined('_ZEXEC') or die;

class WechatAPI {

    /**
     * Establish an http request with curl.
     * @param string $url
     * @param string $postData <i>[Optional]</i>
     * @param int $timeout <i>[Optional]</i>
     * @param string $useragent <i>[Optional]</i>
     * 
     * @return mixed Return the result on success, <b>FALSE</b> on failure.
     */
    public static function curl_request($url, $postData, $timeout, $useragent) {
        //Log::addRuntimeLog("curl running...");
        //$time1 = microtime(1);

        if (!isset($timeout) || empty($timeout)) {
            $timeout = 3;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (isset($postData) && !empty($postData)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        if (isset($useragent) && !empty($useragent)) {
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        //$timecount = microtime(1) - $time1;
        //Log::addRuntimeLog("curl close, time count:{$timecount}");

        if ($result === FALSE) {
            Log::addErrorLog("Class:OfficialAPI, https_get_request failed, \$url is $url");
            return FALSE;
        }
        return $result;
    }

    /**
     * check specified privilege bits
     * this function will check the current privilege <b>AND</b> privilege mask value.
     * @param string privilege bits as <b>string</b>
     * <table border="1">
     * <tr align="center">
     * 比特位</td>
     * 4</td>
     * 3</td>
     * 2</td>
     * 1</td>
     * </tr>
     * <tr align="center">
     * 对应权限级</td>
     * 认证服务号</td>
     * 未认证服务号</td>
     * 认证订阅号</td>
     * 未认证订阅号</td>
     * </tr>
     * </table>
     * @throws LowPrivilegeException
     * 
     */
    public static function checkPrivilege($privilegeMask) {
        try {
            if (!((bindec(PRIVILEGE_LEVEL) & bindec($privilegeMask)) > 0)) {
                throw new LowPrivilegeException($privilegeMask);
            }
        } catch (LowPrivilegeException $e) {
            Log::addErrorLog($e->getMessage());
            die();
        }
    }

    /**
     * 
     * @param JsonObject $jsonObj
     * @return mixed Return an ErrorPackage instance or False.
     */
    private static function IsErrorPackage($jsonObj) {
        $j = (Object) $jsonObj;
        if ($j != null) {
            if (property_exists($j, 'errcode') && $j->errcode != 0) {
                return new ErrorPackage($j->errcode, $j->errmsg);
            } else {
                return FALSE;
            }
        } else {
            throw new OfficialAPIException("json package decode failed");
        }
    }

    /**
     * 
     * @param string $appid
     * @param string $appsecret
     * @return mixed when request success, returns a token array:<br>'ACCESS_TOKEN' => <b>string</b> access token, <br>'EXPIRED' => <b>int</b> expired time in second
     * <br><br>when request failed, returns an <b>ErrorPackage</b>
     * @throws OfficialAPIException when get process failed
     */
    public static function getAccessToken($appid, $appsecret) {
        self::checkPrivilege('1111');
        $url = sprintf(
                'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s', $appid, $appsecret);
        if ($accessToken = self::curl_request($url)) {
            if ($accessToken === false) {
                throw new OfficialAPIException("get access token failed");
            } else {
                $j = json_decode($accessToken);
                $err = self::IsErrorPackage($j);
                if ($err === FALSE) {
                    $returnArray['ACCESS_TOKEN'] = $j->access_token;
                    $returnArray['EXPIRED'] = $j->expires_in;
                    return $returnArray;
                } else {
                    return $err;
                }
            }
        } else {
            die;
        }
    }

    public static function getServerIP($accessToken) {
        self::checkPrivilege('1111');
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=%s', $accessToken);
        if ($ipList = self::curl_request($url)) {
            $j = json_decode($ipList);
            $err = self::IsErrorPackage($j);
            if ($err === FALSE) {
                $returnArray = $j->ip_list;
                return $returnArray;
            } else {
                return $err;
            }
        } else {
            die;
        }
    }

    public static function checkSignature() {
        self::checkPrivilege('1111');
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        if (!$signature) {
            Log::addRuntimeLog('no signature');
            return;
        }

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        Log::addRuntimeLog("tempStr: $tempStr");

        if ($tmpStr == $signature) {
            echo $echoStr;
            Log::addRuntimeLog("signature valid, echoStr: $echoStr");
            return;
        } else {
            Log::addErrorLog('signature invalid');
            //die;
        }
    }

    /**
     * Convert an XML structure to a WechatMessage structure.
     * @param String $xmlContent [Optional] Default will collect xml data from HTTP_RAW_POST_DATA.
     * @return WechatMessage throw an expection when invalid XML data.
     * @throws OfficialAPIException
     */
    public static function receiveMessage($xmlContent) {
        self::checkPrivilege('1111');
        if (isset($xmlContent)) {
            $postStr = $xmlContent;
        } else {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        }

        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (!$postObj === FALSE) {
                return new WechatMessage((array) $postObj);
            } else {
                throw new OfficialAPIException("receive message, decode post data failed");
            }
        }
    }

    public static function createMenu(WechatMenu $menu) {
        self::checkPrivilege('1111');
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s", ACCESS_TOKEN);
        //echo $url;
        $j = json_encode($menu);
        $result = self::curl_request($url, $j);
        if ($result === FALSE) {
            return FALSE;
        } else {
            return $result;
        }
    }

}

class OfficialAPIException extends Exception {

    /**
     * 
     * @param string $message [Optional]
     * @param int $code [Optional]
     * @param $previous [Optional]
     */
    function __construct($message, $code, $previous) {
        parent::__construct('Official API Exception' . $message, $code, $previous);
    }

}

/**
 * Description of menu
 *
 * @author master
 */
class WechatMenu {

    /**
     *
     * @var array an array of <b>Menu</b> 
     */
    public $button = array();

}

class Menu {

    function __construct($name) {
        $this->name = $name;
    }

    public $name;
    public $sub_button = array();

}

abstract class MenuItem {

    public $type;
    public $name;

}

/**
 * @param string $type available type:<br>
 * click<br>
 * <i>view [not support]</i><br>
 * scancode_push<br>
 * scancode_waitmsg<br>
 * pic_sysphoto<br>
 * pic_photo_or_album<br>
 * pic_weixin<br>
 * location_select
 */
class MenuItem_key extends MenuItem {

    public $key;

    function __construct($type, $name, $key) {
        $this->type = $type;
        $this->name = $name;
        $this->key = $key;
    }

}

/**
 * @param string $type available type:<br>
 * click<br>
 * <b>view [only support]</b><br>
 * scancode_push<br>
 * scancode_waitmsg<br>
 * pic_sysphoto<br>
 * pic_photo_or_album<br>
 * pic_weixin<br>
 * location_select
 */
class MenuItem_url extends MenuItem {

    public $url = 'http://www.sincegrown.com/';

    function __construct($type, $name, $url) {
        $this->type = $type;
        $this->name = $name;
        $this->url = $url;
    }

}
