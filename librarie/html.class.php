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
class HTML {
    public static function markedImg($url, $width = null, $height = null, $extra = "") {
        $html = '<img src="%s" %s %s>';
        $src = ZPATH_IMAGE_SERVER . '/basic/small.gif';
        $imgSrc = "";
        $other = "";
        if (!empty($url)) {
            $imgSrc = "imgsrc=\"{$url}\"";
        }
        if (!empty($width)) {
            $other .= ' width="%s" ';
            $other = sprintf($other, $width);
        }
        if (!empty($height) ) {
            $other .= ' height="%s" ';
            $other = sprintf($other, $height);
        }
        if (!empty($extra)) {
            $other .= $extra;
        }
        $html = sprintf($html, $src, $imgSrc, $other);
        return $html;
    }
}
