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
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Wechat Message Structure
 * @param message_properties Valid properties are as follows
 * <br>
 * <table align="left" border="1">
 * <tr>
 * <td><b>Common Properties</b></td>
 * <td>ToUserName</td>
 * <td>FromUserName</td>
 * <td>CreateTime</td>
 * <td>MsgId<i>(received message(except events) only)</i></td>
 * <td><i><b>MsgType</b></i></td>
 * </tr>
 * </table>
 * <br>
 * <table align="left" border="1">
 * <tr>
 * <td rowspan="5">Special Property Set.<br><br><br>Property Name are <b>Bold</b>,<br>Property value are <i>italic</i></td>
 * <td><b>MsgType</b></td>
 * <td><i>text</i></td>
 * <td><i>image</i></td>
 * <td><i>voice</i></td>
 * <td><i>video</i></td>
 * <td><i>location</i></td>
 * <td><i>link</i></td>
 * <td><i>event</i></td>
 * </tr>
 * <tr>
 * <td rowspan="4">Properties</td>
 * <td><b>Content</b></td>
 * <td><b>MediaId</b></td>
 * <td><b>MediaId</b></td>
 * <td><b>MediaId</b></td>
 * <td><b>Location_X</b></td>
 * <td><b>Title</b></td>
 * <td><b>Event</b></td>
 * </tr>
 * <tr>
 * <td> </td>
 * <td><b>PicUrl</b></td>
 * <td><b>Format</b></td>
 * <td><b>ThumbMediaId</b></td>
 * <td><b>Location_Y</b></td>
 * <td><b>Description</b></td>
 * <td rowspan="3"><b><i>other see next form</i></b></td>
 * </tr>
 * <tr>
 * <td> </td>
 * <td> </td>
 * <td><b>Recognition</b><i>[Optional]</i></td>
 * <td> </td>
 * <td><b>Scale</b></td>
 * <td><b>Url</b></td>
 * </tr>
 * <tr>
 * <td> </td>
 * <td> </td>
 * <td> </td>
 * <td> </td>
 * <td><b>Label</b></td>
 * <td> </td>
 * </tr>
 * </table>
 * <br>
 * <table align="left" border="1">
 * <tr>
 * <td rowspan="4"><b>EVENT</b></td>
 * <td><b>Event</b></td>
 * <td><i>subscribe</i></td>
 * <td><i>unsubscribe</i></td>
 * <td><i>SCAN</i></td>
 * <td><i>LOCATION</i></td>
 * <td><i>CLICK</i></td>
 * <td><i>VIEW</i></td>
 * </tr>
 * <tr>
 * <td rowspan="3"><b>Property</b></td>
 * <td><b>EventKey</b><i>[Can be empty]</i></td>
 * <td><b>EventKey</b><i>[Empty]</i></td>
 * <td><b>EventKey</b></td>
 * <td><b>Latitude</b></td>
 * <td><b>EventKey</b></td>
 * <td><b>EventKey</b></td>
 * </tr>
 * <tr>
 * <td><b>Ticket</b><i>[Opt]</i></td>
 * <td><b> </b></td>
 * <td><b>Ticket</b></td>
 * <td><b>Longitude</b></td>
 * <td><b> </b></td>
 * <td><b> </b></td>
 * </tr>
 * <tr>
 * <td><b> </b></td>
 * <td><b> </b></td>
 * <td><b> </b></td>
 * <td><b>Precision</b></td>
 * <td><b> </b></td>
 * <td><b> </b></td>
 * </tr>
 * </table>
 * @author master
 */
class WechatMessage {

    private $data = array();
    private $CommonProperties = array(
        'ToUserName' => '',
        'FromUserName' => '',
        'CreateTime' => '',
        'MsgId' => '',
        'MsgType' => ''
    );
    private $SpecialProperties = array(
        "Content" => '',
        "MediaId" => '',
        "PicUrl" => '',
        "Format" => '',
        "ThumbMediaId" => '',
        "Location_X" => '',
        "Location_Y" => '',
        "Scale" => '',
        "Label" => '',
        "Title" => '',
        "Description" => '',
        "Url" => ''
    );

    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            throw new UnexpectedValueException;
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        if (isset($this->$name)) {
            unset($this->data[$name]);
        }
    }

    /**
     * 
     * @param array $dataArray
     */
    function __construct(array $dataArray) {
        foreach ($dataArray as $name => $value) {
            $this->$name = $value;
        }
    }
    
    function __clone() {
        if (isset($this->Articles)) {
            $this->Articles = clone $this->Articles;
        }
    }


    public function toXML($trimContentSize = FALSE) {
        if ($trimContentSize && isset($this->Content)) {
//$this->Content = mb_strcut($this->Content, 0, 2020, 'UTF-8').'....';
//Log::addRuntimeLog("trim content. Result: {$this->Content}");
        }
        if (count($this->data) <= 0) {
            throw new UnexpectedValueException;
        }
        if (isset($this->Articles) && is_a($this->Articles, 'Articles')) {
            $this->ArticleCount = $this->Articles->count();
        }
        $xml = "<xml>";
        $lineTpl = "<%s>%s</%s>";
//note Articles can not convert by this function directly. Use $this->Article->toXML.
        foreach ($this->data as $name => $value) {
            if (is_a($value, 'Article')) {
                Log::addRuntimeLog('wechat message, Property is Article. property name: ' . $name);
                $trueValue = $value->toXML();
                Log::addRuntimeLog("true value is $trueValue");
            } else {
                Log::addRuntimeLog('wechat message, Property is not Article. property name: ' . $name);
                $trueValue = '<![CDATA[' . $value . ']]>';
            }
            $xml .= sprintf($lineTpl, $name, $trueValue, $name);
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * clear CommonProperties except SpecialProperties and MsgType.
     * @return WechatMessage
     */
    public function trim() {
        $returnValue = clone $this;
        foreach ($this->CommonProperties as $name => $value) {
            unset($returnValue->$name);
        }
        /*$returnValue = new WechatMessage(array_diff_key($this->data,
                        self::CommonProperties));*/
        $returnValue->MsgType = $this->MsgType;
        return $returnValue;
    }

    /**
     * 
     * @return string a list of current properties, one per line.
     */
    public function toString() {
        $returnStr = "";
        foreach ($this->data as $name => $value) {
            if (is_a($value, 'Article')) {
                $trueValue = $value->toXML;
            } else {
                $trueValue = $value;
            }
            $returnStr .= "{$name} => {$trueValue}" . PHP_EOL;
        }
        return $returnStr;
    }    
}

/**
 * Properties: Title, Description, PicUrl, Url
 */
class Articles {

    private $items = array();

    public function count() {
        return count($this->items);
    }

    /**
     * add an item. param in "" as empty, in <b>null</b> as a placeholder.
     * @param type $Title
     * @param type $Description
     * @param type $PicUrl
     * @param type $Url
     */
    public function addItem($Title, $Description, $PicUrl, $Url) {
        $item = array();
        if (isset($Title) && $Title !== null) {
            $item['Title'] = $Title;
        }
        if (isset($Description) && $Description !== null) {
            $item['Description'] = $Description;
        }
        if (isset($PicUrl) && $PicUrl !== null) {
            $item['PicUrl'] = $PicUrl;
        }
        if (isset($Url) && $Url !== null) {
            $item['Url'] = $Url;
        }
        $this->items[] = $item;
    }

    public function toXML($trimContentSize = FALSE) {
        //Log::addRuntimeLog('article to xml proccessing...');
        if (count($this->items) <= 0) {
            throw new UnexpectedValueException;
        }
        $xml = "";
        $lineTpl = "<%s><![CDATA[%s]]></%s>";
        foreach ($this->items as $value) {
            $xml .= '<item>';
            foreach ($value as $name => $content) {
                $xml .= sprintf($lineTpl, $name, $content, $name);
            }
            $xml .= '</item>';
        }
        return $xml;
    }

}
