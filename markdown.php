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

defined('_ZEXEC') or define("_ZEXEC", 1);
require_once 'base.php';

$path = ZPATH_SERVER_ROOT . $_REQUEST['path'];
if (!file_exists($path)) {
    header("HTTP/1.0 404 Not Found");
    echo "Error: File Not Found!";
    die;
}
$content = file_get_contents($path);
//$title = strstr($content, "\n");
$title = strtok($content, "\n");
$title = explode("#", $title, 2);
$title = end($title);
$title = trim($title);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?> - 4Oranges Blog </title>
        <script src="/librarie/js/jquery.js"></script>
        <script src="/librarie/js/marked.js"></script>
        <link rel="stylesheet" href="/librarie/css/base.css">
        <link rel="stylesheet" href="/librarie/css/blogarticale.css">
        <script src="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/highlight.min.js"></script>
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.3.0/styles/default.min.css">
        <style>
            #control {
                position: fixed;
                height: 240px;
                bottom: 20px;
                right: 0;
            }
            #control div {
                margin-top: 5px;
                margin-right: 10px;
                border: black solid thin;
                width: 70px;
                height: 70px;
                text-align: center;
                align-content: center;
                line-height: 35px;
                font-size: 1em;
                font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans", "wenquanyi micro hei", "Hiragino Sans GB", "Hiragino Sans GB W3", "FontAwesome", sans-serif;
                float: right;
            }
            #control .back{
                line-height: 70px;
            }
            #control div:hover {
                border-color: red;
                color: red;
                cursor:pointer;
            }
            pre {
                margin-left: 40px;
                margin-right: 40px;
                padding: 20px;
                background-color: rgb(245, 245, 245);
            }
        </style>
        <script>
            var content = <?php echo json_encode($content); ?>;
        </script>
    </head>
    <body>
        <div class="content">
            <div class="blogarticale" id="markdown"></div>
        </div>
        <div id="control">
            <div class="back clear">HOME</div>
            <div class="html clear">Show HTML</div>
            <div class="md clear">Show MD</div>
        </div>
    </body>
    <script>
        marked.setOptions({
            highlight: function (code, lang) {
                console.log(lang);
                if (lang === undefined) {
                    return hljs.highlightAuto(code).value;
                } else {
                    return hljs.highlight(lang, code).value;
                }
            }
        });
        $("#control .md").click(function () {
            $("#markdown").html("<pre>" + content + "</pre");
        });
        $("#control .back").click(function () {
            window.location.href = "/";
        });
        $("#control .html").click(function () {
            $("#markdown").html(marked(content));
        });

        $("#control .html").click();
    </script>
</html>