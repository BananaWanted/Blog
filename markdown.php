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

function get_article($path) {
    if (!file_exists($path)) {
        header("HTTP/1.0 404 Not Found");
        echo "Error: File Not Found!";
        die;
    }

    $content = file_get_contents($path);
    $content_len = strlen($content);
    $output = array(
        "title" => "",
        "meta" => array(),
        "content" => ""
    );

    $delimiter_beg = "```metadata\n";
    $delimiter_beg_len = strlen($delimiter_beg);
    $delimiter_end = "```\n";
    $delimiter_end_len = strlen($delimiter_end);

    $delimiter_beg_pos = stripos($content, $delimiter_beg);
    if ($delimiter_beg_pos !== FALSE) {
        $delimiter_end_pos = stripos($content, $delimiter_end, $delimiter_beg_pos + $delimiter_beg_len);

        $temp = explode("\n", substr($content, $delimiter_beg_pos + $delimiter_beg_len, $delimiter_end_pos - $delimiter_beg_pos - $delimiter_beg_len));
        foreach ($temp as $value) {
            $pos1 = strpos($value, ":");
            $pos2 = strpos($value, "=");
            if ($pos1 && $pos2) {
                $pos = min($pos1, $pos2);
            } else {
                $pos = $pos1 or $pos2;
            }
            if ($pos === FALSE) {
                continue;
            }
            $output["meta"][trim(substr($value, 0, $pos))] = trim(substr($value, $pos + 1));
        }
        $output["content"] = substr_replace($content, "", $delimiter_beg_pos, $delimiter_end_pos - $delimiter_beg_pos + $delimiter_end_len);
    } else {
        $temp = explode("\n", $content, 3);
        $output["meta"]["date"] = trim($temp[1]);
        $output["content"] = $temp[0] . "\n" . $temp[2];
    }

    $output["title"] = trim(
            explode("#", explode("\n", $content, 2)[0]
            )[1]
    );
    if (isset($output["meta"]["keyword"]) && !empty($output["meta"]["keyword"])) {
        $keywords = explode(",", $output["meta"]["keyword"]);
        foreach ($keywords as &$value) {
            $value = trim($value);
        }
        $output["meta"]["keyword"] = $keywords;
    }
    return $output;
}

function scan_articles($path) {
    $article_dir = new DirectoryIterator($path);
    $output = array();
    foreach ($article_dir as $entry) {
        if ($entry->getExtension() == "md") {
            $output[$entry->getPathname()] = [
                "basename" => $entry->getBasename(".md"),
                "filename" => $entry->getFilename(),
                "pathname" => $entry->getPathname(),
                "content" => get_article($entry->getPathname())
            ];
        }
    }
    return $output;
}

$scan_path = ZPATH_SERVER_ROOT . DIRECTORY_SEPARATOR . "articles";
$article_path = ZPATH_SERVER_ROOT . $_REQUEST['path'];
$overview = scan_articles($scan_path);

$output;
$menu = array();

if (!isset($_REQUEST['path']) || empty($_REQUEST['path']) || $_REQUEST['path'] == "/index.md") {
    $output = get_article($article_path);
} else {
    $output = $overview[$article_path]["content"];
}
foreach ($overview as $key => $value) {
    $temp = $value["content"];
    unset($temp["content"]);
    $temp["path"] = DIRECTORY_SEPARATOR . "articles" . DIRECTORY_SEPARATOR . $value["filename"];
    $menu[] = $temp;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <!--meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' /-->
        <title><?php echo $output["title"]; ?> - 4Oranges Blog<?php
            if (isset($output["meta"]["keyword"]) && !empty($output["meta"]["keyword"])) {
                echo " - ";
                echo implode(" | ", $output["meta"]["keyword"]);
            }
        ?></title>

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
                background-color: rgba(255,255,255,0.5);
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

            #menu {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 30em;
                height: 70%;
                background: white;
                overflow-x: hidden;
                overflow-y: scroll;
                border-right: solid 1px;
                border-bottom: solid 1px;
                border-color: #e5e5e5 #dbdbdb #d2d2d2;
                -webkit-box-shadow: rgba(0, 0, 0, 0.3) 0 1px 3px;
                -moz-box-shadow: rgba(0, 0, 0, 0.3) 0 1px 3px;
                box-shadow: rgba(0, 0, 0, 0.3) 0 1px 3px;
                z-index: 0;
                border-radius: 0 0 20px 0;
                display: none;
            }
            #menu-button {
                cursor: pointer !important;
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 3em;
                height: 3em;
                background: plum;
                margin: 1em 0 0 1em;
                z-index: 1;
            }
            #menu-content {
                display: block;
                margin: 5em 1em auto 1em;
            }
            .menu-item {
                cursor: pointer !important;
                display: block;
                padding: 0.5em 1em 0.5em 1em;
                border-collapse: collapse;
                line-height: 2em;
                border-top: solid thin rgba(251,152,255,0.4);
            }
            .menu-item:hover {
                background: #fdffc8;
            }
            .menu-item-widgets div {
                float: right;
                font-size: 0.8em;
                margin-left: 1em;
                padding-left: 1em;
                /*height: 0.8em;*/
                color: #aaa;
            }
        </style>
        <script>
            var content = <?php echo json_encode($output); ?>;
            var menu = <?php echo json_encode($menu); ?>;
            var path = '<?php echo $_REQUEST['path']; ?>';
            var disable_disqus_on = [
                "/index.md",
                "/README.md"
            ];
        </script>
    </head>
    <body>
        <div id="container">
            <div id="background"></div>
            <div id="menu-button"></div>
            <div id="menu">
                <div id="menu-content" class="selfclear"></div>
            </div>
            <div class="blogarticle">
                <div id="markdown">
                    <noscript><pre><?php echo $output["content"]; ?></pre></noscript>
                </div>
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
        var $container = $("#container");
        var $background = $("#background");
        var $article = $(".blogarticle");
        var $markdown = $("#markdown");
        var $discuss = $("#disqus_thread");
        var $menu = $("#menu");
        var $menu_button = $("#menu-button");
        var $menu_content = $("#menu-content");

        function jump(href) {
            window.location.href = href;
        }

        $window.resize(function () {
            $container.width($window.width());
            $background.width($window.width()).height($window.height());

            $background.css("background-size", $background.height() / 1.8);
            //console.log($background.css("background-size"));
        }).resize();
        marked.setOptions({
            highlight: function (code, lang) {
                //console.log("highlight language: " + lang);
                //console.log(code);
                //console.log("=============================================================================");
                if (lang === undefined) {
                    return hljs.highlightAuto(code).value;
                } else if (lang === "text") {
                    return code;
                } else {
                    //var debug = hljs.highlight(lang, code).value;
                    //console.log(debug);
                    return hljs.highlight(lang, code).value;
                }
            }
        });
        $menu_button.click(function (a) {
            $menu.toggle(300);
        });
        
        menu.forEach(function(value) {
            var template = `
            <div class="menu-item" href="${value.path}" alt="${value.title}" onclick="jump('${value.path}')">
                <div class="menu-item-title">${value.title}</div>
                <div class="menu-item-widgets selfclear">
                    <div class="menu-item-status" ${function(){
                            if (value.meta.status == "complete") {
                                return 'style="color: green;"';
                            } else {
                                return 'style="color: red;"';
                            }
                    }()}>${function(){
                            if (value.meta.status == "complete") {
                                return "已完成";
                            } else {
                                return value.meta.status;
                            }
                    }()}</div>
                    <div class="menu-item-date">${value.meta.date}</div>
                </div>
            </div>
            `.trim();
            $menu_content.append(template);
        });
        
        $("#control .back").click(function () {
            jump('/');
            //window.location.href = "/";
        });

        $("#control .md").click(function () {
            $markdown.html("<pre>" + content.content + "</pre");
            $discuss.hide();
        });

        $("#control .html").click(function () {
            $markdown.
                    html(marked(content.content)).
                    append('<p class="time">' + content.meta.date + '</p>');
            $markdown.find(':header[id]:not(h1)').addClass('anchor').click(function (e) {
                jump('#' + e.target.id);
            });
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
                //console.log(path);
                $discuss.show();
            }
        });
        $("#control .html").click();
    </script>
</html>