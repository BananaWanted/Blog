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
$elements = explode("\n", $content, 3);

$title = explode("#", $elements[0], 2);
$title = end($title);
$title = trim($title);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title; ?> - 4Oranges Blog </title>

        <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-TileImage" content="/mstile-144x144.png">
        <meta name="theme-color" content="#ffffff">

        <script src="/librarie/js/jquery.js"></script>
        <script src="/librarie/js/marked.js"></script>
        <link rel="stylesheet" href="/librarie/css/base.css">
        <link rel="stylesheet" href="/librarie/css/blogarticle.css">
        <script src="/librarie/js/highlight.min.js"></script>
        <link rel="stylesheet" href="/librarie/css/highlight.min.css">
        <script src="/librarie/js/imagesloaded.min.js"></script>
        <style>
            #background {
                background-position: bottom left;
                /*background-repeat: no-repeat;*/
                background-image: url(/banana.jpg);
                background-size: 700px;
                background-repeat: no-repeat;
                position: fixed;
                z-index: -10;
                top: 0;
                left: 0;
            }
            #control {
                position: fixed;
                height: 240px;
                bottom: 20px;
                right: 0;
            }
            #control div {
                background-color: rgba(255,255,255,0.95);
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
            #disqus_thread {
                margin-top: 10em;
                display: none;
            }
            pre {
                margin-left: 40px;
                margin-right: 40px;
                padding: 20px;
                background-color: rgb(245, 245, 245);
            }
        </style>
        <script>
            var content = <?php echo json_encode($elements); ?>;
            var path = '<?php echo $_REQUEST['path']; ?>';
            var disable_disqus_on = [
                "index.md",
                "README.md"
            ];
        </script>
    </head>
    <body>
        <div id="container">
            <div id="background"></div>
            <div class="blogarticle">
                <div id="markdown"></div>
                <div id="disqus_thread"></div>
            </div>
            <div id="control">
                <div class="back clear">HOME</div>
                <div class="html clear">Show<br>HTML</div>
                <div class="md clear">Show<br>MD</div>
            </div>
        </div>
    </body>
    <script>

        var disqus_config = function () {
            this.page.url = 'http://blog.fuckcugb.com' + path;
            this.page.identifier = path;
            //console.log(this.page.url);
            //console.log(this.page.identifier);
        };
        if (disable_disqus_on.indexOf(path) < 0) {
            (function () {
                var d = document, s = d.createElement('script');

                s.src = '//4oranges.disqus.com/embed.js';

                s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        }
    </script>
    <script>
        var $window = $(window);
        var $article = $(".blogarticle");
        var $container = $("#container");
        var $discuss = $("#disqus_thread");
        var $markdown = $("#markdown");

        $window.resize(function () {
            $("#background").width($window.width()).height($window.height());
            $("#container").width($window.width());
        }).resize();
        marked.setOptions({
            highlight: function (code, lang) {
                if (lang === undefined) {
                    return hljs.highlightAuto(code).value;
                } else {
                    return hljs.highlight(lang, code).value;
                }
            }
        });

        $("#control .back").click(function () {
            window.location.href = "/";
        });

        $("#control .md").click(function () {
            $markdown.html("<pre>" + content.join("\n") + "</pre");
            $discuss.hide();
        });

        $("#control .html").click(function () {
            $markdown.
                    html(marked(content[0] + "\n" + content[2])).
                    append('<p class="time">' + content[1] + '</p>');
            var img_width = $(".blogarticle p").width();
            $article.imagesLoaded().progress(function (loded, img) {
                if (img.isLoaded) {
                    if (img.img.naturalWidth > img_width) {
                        $(img.img).css("width", img_width);
                    } else {
                        $(img.img).css("width", img.img.naturalWidth);
                    }
                }
            });
            if (disable_disqus_on.indexOf(path) < 0) {
                $discuss.show();
            }
        });
        $("#control .html").click();
    </script>
</html>