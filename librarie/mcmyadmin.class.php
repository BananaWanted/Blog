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
 * McMyAdminAPI
 *
 * @author master
 */
class McMyAdmin {

    private $MCMASESSIONID = "";
    private $url = "";
    private $since = -1;

    //public $isLogin = FALSE;

    /**
     *
     * @param String $url request url, like http://mc.fuckcugb.com/data.json
     */
    public function __construct($url) {
        if (isset($url) && !empty($url)) {
            $this->url = $url;
        }
    }

    /**
     * Initialize global McMyAdmin object in $_SESSION['MC']
     * @param String $url <i>[Optional]</i>
     */
    public static function init($url) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($url) || empty($url)) {
            $url = "";
        }
        $_SESSION['MC'] = new McMyAdmin($url);
    }

    /**
     * If the <b>$isLogin</b> is set to <b>TRUE</b>, this method will return immediately.
     *
     * @param String $username
     * @param String $password <i>[Optional]</i>
     * @param String $token <i>[Optional]</i>
     * @param String $url <i>[Optional]</i>
     * @return boolean TRUE on success. FALSE on failed.
     */
    public function Login($username, $password = "", $token = "", $url) {
        /* if ($this->isLogin) {
          return TRUE;
          } */
        $post = 'Username=%s&Password=%s&Token=%s&req=login';
        if (!isset($url) || empty($url)) {
            $url = $this->url;
        } else {
            $this->url = $url;
        }
        $data = sprintf($post, urlencode($username), urlencode($password), urlencode($token));
        $rel = Base::browser_request($url, $data);
        //var_dump($rel);
        if ($rel !== FALSE) {
            $json = json_decode($rel);
            if ($json->success && isset($json->MCMASESSIONID)) {
                $this->MCMASESSIONID = $json->MCMASESSIONID;
                //$this->isLogin = TRUE;
                $this->since = -1;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function SendChat($message) {
        $post = 'Message=%s&req=sendchat&MCMASESSIONID=%s';
        $url = $this->url;
        $data = sprintf($post, urlencode($message), urlencode($this->MCMASESSIONID));
        $rel = Base::browser_request($url, $data);
        if ($rel === FALSE || json_decode($rel)->success) {
            return FALSE;
        } else {
            return TRUE;
        }
        //var_dump($rel);
    }

    /**
     *
     * @param Int $since <i>[Optional]</i> Default is the <i>timestamp</i> variable returned from remote server last time. <i>since</i> variable is initialized with -1, which means request all available chats.
     * @return boolean
     */
    public function GetChat($since) {
        $fuck;
        if (!isset($since)) {
            $fuck = $this->since;
        } else {
            $fuck = $since;
            $this->since = $since;
        }
        $post = 'Since=%s&req=getchat&MCMASESSIONID=%s';
        $url = $this->url;
        $data = sprintf($post, urlencode($fuck), urlencode($this->MCMASESSIONID));
        $rel = Base::browser_request($url, $data);
        if ($rel !== FALSE) {
            $json = json_decode($rel);
            if ($json->status == 200) {
                $this->since = $json->timestamp;
                return $json->chatdata;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * /Fill blocks in the game.<br>
     * Arguments will be passed to the game whitout syntax check by calling the <i>SendChat</i> method directly.<br>
     * If the first argument is an array, this array will be treat as filled with required arguments and will passed to the game separately, and other arguments will be ignored.<br>
     * ===========================================<br>
     * <i>instruction from:http://minecraft.gamepedia.com/Commands</i><br>
     * <b>Syntax:</b><br>
     * &nbsp;&nbsp;&nbsp;&nbsp;fill <x1> <y1> <z1> <x2> <y2> <z2> <TileName> [dataValue] [oldBlockHandling] [dataTag]<br>
     * The fill command also has an optional alternate syntax when using the replace option:<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;fill <x1> <y1> <z1> <x2> <y2> <z2> <TileName> <dataValue> replace [replaceTileName] [replaceDataValue]<br>
     * <b>Arguments：</b><br>
     * &nbsp;&nbsp;&nbsp;&nbsp;x1 y1 z1 and x2 y2 z2<br>
     * Specifies any two opposing corner blocks of the region to be filled (the "fill region"). May use tilde notation to specify distances relative to the command's execution.<br>
     * The blocks that make up the corners extend in the positive direction from the coordinates used to identify them. Because of this, the lesser coordinates of each axis will be right on the region boundary, but the greater coordinates will be one block from the boundary, and the block volume of the source region will be (xgreater - xlesser + 1) × (ygreater - ylesser + 1) × (zgreater - zlesser + 1). For example, 0 0 0 0 0 0 has a 1-block volume, and 0 0 0 1 1 1 and 1 1 1 0 0 0 both identify the same region with an 8-block volume.<br>
     * <b>TileName</b><br>
     * Specifies the block to fill the region with. Must be a block id (for example, minecraft:stone).<br>
     * <b>dataValue (optional)</b><br>
     * Specifies the block data to use for the fill block. Must be between 0 and 15 (inclusive).<br>
     * <b>oldBlockHandling (optional)</b><br>
     * Must be one of:<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;destroy - Replaces all blocks (including air) in the fill region with the specified block, dropping the existing blocks (including those that are unchanged) and block contents as entities as if they had been mined with an unenchanted diamond shovel or pickaxe. (Blocks that can only be mined with shears, such as vines, will not drop; neither will liquids.)<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;hollow - Replaces only blocks on the outer edge of the fill region with the specified block. Inner blocks are changed to air, dropping their contents as entities but not themselves.<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;keep - Replaces only air blocks in the fill region with the specified block.<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;outline - Replaces only blocks on the outer edge of the fill region with the specified block. Inner blocks are not affected.<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;replace - Replaces all blocks (including air) in the fill region with the specified block, without dropping blocks or block contents as entities. Optionally, instead of specifying a data tag for the replacing block, block id and data values may be specified to limit which blocks are replaced (see replaceTileName and replaceDataValue below)<br>
     * If not specified, defaults to replace.<br>
     * <b>dataTag (optional)</b><br>
     * Specifies the data tag to use for the fill block (for example, contents of a chest, patterns on a banner, etc.). Must be a compound NBT tag (for example, {CustomName:Fred}). Cannot be combined with the replaceTileName and replaceDataValue arguments.<br>
     * <b>replaceTileName replaceDataValue (optional)</b><br>
     * Arguments are only valid when oldBlockHandling is replace. Cannot be combined with the dataTag argument.<br>
     * Specifies the block id and data of the blocks in the fill region to be replaced. If replaceDataValue is not specified, data value is ignored when determining which blocks to replace. If both arguments are not specified, replaces all blocks in the fill region.<br>
     * <b>Result</b><br>
     * Fails if the arguments are not specified correctly, if the fill region is not rendered, if the block volume of the fill region is greater than 32768, if dataValue or dataTag are invalid for the specified block id, or if no blocks were changed.<br>
     * On success, changes blocks in the fill region to the specified block.<br>
     *
     * @param int $x1 <b>REQUIRED</b>
     * @param int $y1 <b>REQUIRED</b>
     * @param int $z1 <b>REQUIRED</b>
     * @param int $x2 <b>REQUIRED</b>
     * @param int $y2 <b>REQUIRED</b>
     * @param int $z2 <b>REQUIRED</b>
     * @param string $TileName <b>REQUIRED</b>
     * @param int $dataValue Recommended.
     * @param string $oldBlockHandling Require above arguments NOT empty.
     * @param mixed $others Can be multiple values.
     * @param bool $positive If true, and and y value is bigger than 256, the method will treat the y value as 256, instead of doing nothing but return false. <br><b>NOTE: </b>If you use more than one value for $others, the default value of $positive will be overwritten and you should specify the value of $positive explicitly.
     */
    public function Fill($x1, $y1, $z1, $x2, $y2, $z2, $TileName, $dataValue, $oldBlockHandling = '', $others = null, $positive = true) {
        if (is_array($x1)) {
            return call_user_func([$this, 'Fill'], $x1);
        }
        $args = func_get_args();
        $pos = func_get_arg(func_num_args() - 1);
        if ($y1 > 256) {
            if ($pos) {
                $args[1] = 256;
                return call_user_func([$this, 'Fill'], $args);
            } else {
                return FALSE;
            }
        }
        if ($y2 > 256) {
            if ($pos) {
                $args[4] = 256;
                return call_user_func([$this, 'Fill'], $args);
            } else {
                return FALSE;
            }
        }
        array_pop($args);
        if ((abs($x1 - $x2) + 1) * (abs($y1 - $y2) + 1) * (abs($z1 - $z2) + 1) >= 32768) {
            if (strtolower($oldBlockHandling) == 'hollow' || strtolower($oldBlockHandling) == 'outline') {
                $innerArgs = $args;
                if ($innerArgs[0] >= $innerArgs[3]) {
                    --$innerArgs[0];
                    ++$innerArgs[3];
                } else {
                    ++$innerArgs[0];
                    --$innerArgs[3];
                }
                if ($innerArgs[1] >= $innerArgs[4]) {
                    --$innerArgs[1];
                    ++$innerArgs[4];
                } else {
                    ++$innerArgs[1];
                    --$innerArgs[4];
                }
                if ($innerArgs[2] >= $innerArgs[5]) {
                    --$innerArgs[2];
                    ++$innerArgs[5];
                } else {
                    ++$innerArgs[2];
                    --$innerArgs[5];
                }
                $innerArgs[6] = 'air';
                $innerArgs[7] = 0;
                if (strtolower($oldBlockHandling) == 'hollow') {
                    $innerArgs[8] = 'destroy';
                } else {
                    $innerArgs[8] = 'replace';
                }
                call_user_func([$this, 'Fill'], $innerArgs);
                $args[8] = 'replace';
                $innerArgs = $args;
                $innerArgs[1] = $innerArgs[4];
                call_user_func([$this, 'Fill'], $innerArgs);
                $args[1] >= $args[4] ? ++$args[4] : --$args[4];

                $innerArgs = $args;
                $innerArgs[4] = $innerArgs[1];
                call_user_func([$this, 'Fill'], $innerArgs);
                $args[4] >= $args[1] ? ++$args[1] : --$args[1];

                $innerArgs = $args;
                $innerArgs[3] = $innerArgs[6];
                call_user_func([$this, 'Fill'], $innerArgs);
                $args[3] >= $args[6] ? ++$args[6] : --$args[6];

                $innerArgs = $args;
                $innerArgs[6] = $innerArgs[3];
                call_user_func([$this, 'Fill'], $innerArgs);
                $args[6] >= $args[3] ? ++$args[3] : --$args[3];

                $innerArgs = $args;
                $innerArgs[2] = $innerArgs[5];
                call_user_func([$this, 'Fill'], $innerArgs);

                $innerArgs = $args;
                $innerArgs[5] = $innerArgs[2];
                call_user_func([$this, 'Fill'], $innerArgs);
                return TRUE;
            } else {
                if (abs($x1 - $x2) > 0) {
                    $half = (int) (($x1 + $x2) / 2);
                    $fuck = $x2;
                    $args[3] = $half;
                    call_user_func([$this, 'Fill'], $args);
                    $args[3] = $fuck;
                    $args[0] = $half + 1;
                    call_user_func([$this, 'Fill'], $args);
                } elseif (abs($y1 - $y2) > 0) {
                    $half = (int) (($y1 + $y2) / 2);
                    $fuck = $y2;
                    $args[4] = $half;
                    call_user_func([$this, 'Fill'], $args);
                    $args[4] = $fuck;
                    $args[1] = $half + 1;
                    call_user_func([$this, 'Fill'], $args);
                } elseif (abs($z1 - $z2) > 0) {
                    $half = (int) (($z1 + $z2) / 2);
                    $fuck = $z2;
                    $args[5] = $half;
                    call_user_func([$this, 'Fill'], $args);
                    $args[5] = $fuck;
                    $args[2] = $half + 1;
                    call_user_func([$this, 'Fill'], $args);
                }
                return TRUE;
            }
        }
        $data = '/fill ';
        $data .= join(' ', $args);
        return $this->SendChat($data);
    }

    public function Diagnose() {
        var_dump($this);
        return json_encode($this);
    }

}
