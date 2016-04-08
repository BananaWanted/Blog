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

/**
 * Description of robots
 *
 * @author master
 */
class Robots {

    private $key = "3a2d068937986e96c8cdba85d4ee41b7";
    private $url = "http://www.tuling123.com/openapi/api";

    /**
     * 
     * @param string $Msg message to send
     * @param string $id user id
     * 
     * @return string response
     */
    public function textRequest($Msg, $id) {
        $url = $this->url . "?key=%s&info=%s&userid=%s";
        $url = sprintf($url, $this->key, urlencode($Msg), $id);
        $result = WechatAPI::curl_request($url);
        Log::addRuntimeLog("robot request: $result");
        if ($result === FALSE) {
            return FALSE;
        }
        $j = json_decode($result);
        if ($j !== FALSE && $j->code == 100000) {
            //Log::addRuntimeLog("robots: code: {$j->code}, text: {$j->text}");
            return $j->text;
        } else {
            return FALSE;
        }
    }

    /**
     * Determine whether a string is a robot command.
     * @param string $str
     * @return boolean
     */
    public function isCommand($str) {
        if (strpos($str, '::') !== FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param string $command a string formats "<b> :: </b><i>prefix</i><b> :: </b><i>content</i><b> :: </b><i>content</i><b> :: </b>...".
     * prifix can be: a<i>(nswer)</i>, ...
     * @return mixed return TRUE, or other depending on the command. If command is invalid, or proccess faild, return FALSE.
     */
    public function command($command) {
        Log::addRuntimeLog("receive command: $command");
        $parts = explode('::', $command);
        array_shift($parts);
        //$j = json_encode($parts);
        //Log::addRuntimeLog("parts: $j");
        if ($prefix = array_shift($parts)) {
            //Log::addRuntimeLog("prefix: $prefix");
            switch ($prefix) {
                case 'a':
                    if (count($parts) === 2 && preg_match('/^\d*$/', $parts[0]) == 0) {
                        //Log::addRuntimeLog('command: a');
                        if ($this->teach(array_shift($parts),
                                        array_shift($parts))) {
                            return '学习做人成功';
                        } else {
                            return FALSE;
                        }
                        //return TRUE;
                    } else {
                        return FALSE;
                    }
                case 'g':
                    //log::addRuntimeLog("command 'g'.");
                    return $this->googleSearch(array_shift($parts));
                default:
                    return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param string $receive
     * @param string $reply
     * 
     * @return boolen true on success, false on failure.
     */
    public function teach($receive, $reply) {
        if (is_string($receive) && is_string($reply) && !empty($receive) && !empty($reply)) {
            $receiveMsg = new WechatMessage(array(
                'MsgType' => 'text',
                'Content' => $receive
            ));
            $replyMsg = new WechatMessage(array(
                'MsgType' => 'text',
                'Content' => $reply
            ));

            $db = new DatabaseController();
            $result = $db->setAutoReply($receiveMsg, $replyMsg);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Apply a user function recursively to every member of an array
     * @link http://ylbook.com/cms/web/gugecanshu.htm
     * @param string $query query string
     * @param string $returnOptions <i>[Optional]</i> determine the return data format. Can be empty as a placeholder. default is "simple".<br>
     * <table border="1">
     * <tr>Options<td>debug<td>simple<td>normal
     * <tr>Description<td>raw request data as string from google.com<td>an array as title=>url of the first page. Result number depends on the request option.
     * </table>
     * @param array $requestOptions <i>[Optional]</i> contains all serach option except "q" in query url. For example: hl=>zh-CN.<br>view the web page foo more information.
     * 
     * 
     * @return <b>WechatMessage</b>
     * <br><s>mixed determine by the <b>$returnOption</b> param. Default will return an array as title=>url of the first page(default is 10 records) of result.</s>
     */
    public function googleSearch($query, $returnOptions = 'simple',
            $requestOptions) {

        $url = 'http://ajax.googleapis.com/ajax/services/search/web?';
        Log::addRuntimeLog("command 'g' start.");

        if (!isset($requestOptions['v']) || empty($requestOptions['v'])) {
            $requestOptions['v'] = '1.0';
        }
        if (empty($requestOptions['rsz'])) {
            $requestOptions['rsz'] = 'large';
        }
        if (empty($requestOptions['start'])) {
            $requestOptions['start'] = 0;
        }

        foreach ($requestOptions as $name => $value) {
            $url .= $name . "=" . $value . "&";
        }

        $url .= 'q=' . urlencode($query);

        Log::addRuntimeLog("url: $url");
        $res = WechatAPI::curl_request($url);
        if ($res === FALSE) {
            return FALSE;
        }
        //$doc = new simple_html_dom();
        //$doc->load($res);
        Log::addRuntimeLog("google search result: \n{$res}");
        //return 'testing';

        $j = json_decode($res);

        if ($j->responseData == null) {
            return FALSE;
        }

        $msg = new WechatMessage(array(
            'MsgType' => 'news',
            'CreateTime' => time(),
            'Articles' => new Articles()
        ));

        foreach ($j->responseData->results as $item) {
            $msg->Articles->addItem($item->titleNoFormatting, $item->content,
                    null, $item->url);
        }

        //Log::addRuntimeLog('craft article finished: '.$msg->toXML());
        //$msg->ArticleCount = $msg->Articles->count();
        return $msg;
        /* $li = $doc->find('li.g');
          if (isset($returnOptions))
          {
          if ($requestOptions == 'debug')
          {
          return $res;
          } elseif ($requestOptions == 'simple')
          {
          return 'simple';
          } elseif ($requestOptions == 'normal')
          {
          return 'normal';
          } else
          {
          return FALSE;
          }
          } */
    }

}
