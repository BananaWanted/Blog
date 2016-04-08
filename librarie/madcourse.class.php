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
 * NEED UPDATE to use Base::browser_request();
 * Use $_SESSION['remote_cookie']
 *
 * @author master
 */
class MadCourse {

    public $Lessons = array();

    public function __construct() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function preload($CollegeID = "CUGB") {
        switch (strtoupper(trim($CollegeID))) {
            case "CUGB":
                return $this->preload_cugb();
            default:
                return FALSE;
        }
        return FALSE;
    }

    public function load($CollegeID = "", $StudentId = "", $Password = "", $Verification = "") {
        switch (strtoupper(trim($CollegeID))) {
            case "CUGB":
                return $this->load_cugb($StudentId, $Password, $Verification);
            case "SU":
                return $this->load_su($StudentId);
            default:
                return FALSE;
        }
        return FALSE;
    }

    // <editor-fold defaultstate="collapsed" desc="Load methods for specified college.">
    private function preload_cugb() {
        // $this->setCookies('http://202.204.105.22/academic/j_acegi_security_check', "CUGB");
        // return base64_encode(Base::curl_request("http://202.204.105.22/academic/getCaptcha.do", "", $_SESSION["remote_cookie"]));
        return base64_encode(Base::browser_request("http://202.204.105.22/academic/getCaptcha.do"));
    }

    private function load_cugb($StudentId = "", $Password = "", $Verification = "") {
        $post = "j_username={$StudentId}&j_password={$Password}&j_captcha={$Verification}";
        $this->getHtml("http://202.204.105.22/academic/j_acegi_security_check", $post, $_SESSION["remote_cookie"]);
        $data = $this->getHtml('http://202.204.105.22/academic/accessModule.do?groupId=&moduleId=2000', "", $_SESSION["remote_cookie"]);
        $match = array();
        preg_match("/\/manager\/coursearrange\/showTimetable\.do\?id=(\d*)/i", $data, $match);
        $id = $match[1];
        $year = intval(date('Y')) - 1980;
        $month = intval(date('m'));
        $term = 1;
        if ($month >= 8) {
            $term = 3;
        }

        $corseurl = 'http://202.204.105.22/academic/manager/coursearrange/showTimetable.do?id=%s&yearid=%s&termid=%s&timetableType=STUDENT&sectionType=BASE';
        // $corseurl = 'http://202.204.105.22/academic/manager/coursearrange/showTimetable.do?id=%s&yearid=%s&termid=%s&timetableType=STUDENT&sectionType=COMBINE';
        $corseurl = sprintf($corseurl, $id, $year, $term);
        $data = $this->getHtml($corseurl, "", $_SESSION["remote_cookie"]);
        // echo "<pre>=================================\n" . htmlspecialchars($data, ENT_IGNORE) . "</pre>";

        $dom = new simple_html_dom();
        $dom->load($data);
        //echo htmlspecialchars($data);
        for ($week = 1; $week < 8; ++$week) {
            for ($no = 1, $realSeq = 1; $no < 11; $no+=2, ++$realSeq) {
                $node = $dom->find("#{$week}-{$no}");
                if ($node == NULL) {
                    return false;
                }
                $th = $node[0]->parent()->find('th')[0]->innertext;
                $th = $this->formatHTML($th);
                $th = str_ireplace('\n┆\n', '-', $th);
                $noAdd1 = $no + 1;
                $noAdd2 = $no + 2;
                $th = preg_replace(['/08\:50/', '/10\:50/', '/14\:50/', '/16\:50/', '/19\:50/', "/第{$no}节/"], ['09:45', '11:45', '15:45', '17:45', '21:40', $no == 9 ? "第{$no}, {$noAdd1}, {$noAdd2}节" : "第{$no}, {$noAdd1}节"], $th);
                $content = $this->formatHTML($node[0]->innertext);
                $content = preg_replace_callback('/<<(.*?)>>;\d*\b/i', function($matches) {
                    return $matches[1];
                }, $content);
                $this->addLesson(trim($content), trim($th), $week, $realSeq);
            }
        }
        return TRUE;
    }

    private function load_su($StudentId = "") {
        $timeString = '14-15-3';
        $post = "queryStudentId={$StudentId}&queryAcademicYear={$timeString}";
        // $this->setCookies('http://xk.urp.seu.edu.cn/jw_service/service/lookCurriculum.action', 'SU');
        $data = $this->getHtml("http://xk.urp.seu.edu.cn/jw_service/service/stuCurriculum.action", $post, "");
        // echo "<pre>=================================\n" . htmlspecialchars($data, ENT_IGNORE) . "</pre>";
        $dom = new simple_html_dom();
        $dom->load($data);
        $morning = TRUE;
        $afternoon = TRUE;
        $evening = TRUE;
        foreach ($dom->find('[rowspan]') as $node) {
            if ($morning && $node->parent()->first_child()->innertext == "上午") {
                $morning = FALSE;
                $children = $node->parent()->children();
                for ($i = 2; $i < count($children); ++$i) {
                    $content = trim($children[$i]->innertext);
                    $week = $i - 1;
                    $match;
                    $counter = preg_match_all('/(.*?)<br>(.*?)(\d+-\d+节)<br>(.*?)<br>/i', $content, $match);
                    for ($count = 0; $count < $counter; ++$count) {
                        $content = "{$match[1][$count]}{$match[2][$count]}{$match[3][$count]}\n{$match[4][$count]}\n";
                        $time = "上午";
                        $seq = $count + 1;
                        $content = $this->formatHTML($content);
                        $this->addLesson(trim($content), trim($time), $week, $seq);
                    }
                }
            }
            if ($afternoon && $node->parent()->first_child()->innertext == "下午") {
                $afternoon = FALSE;
                $children = $node->parent()->children();
                for ($i = 2; $i < count($children); ++$i) {
                    $content = trim($children[$i]->innertext);
                    $week = $i - 1;
                    $match;
                    $counter = preg_match_all('/(.*?)<br>(.*?)(\d+-\d+节)<br>(.*?)<br>/i', $content, $match);
                    for ($count = 0; $count < $counter; ++$count) {
                        $content = "{$match[1][$count]}{$match[2][$count]}{$match[3][$count]}\n{$match[4][$count]}\n";
                        $time = "下午";
                        $seq = $count + 3;
                        $content = $this->formatHTML($content);
                        $this->addLesson(trim($content), trim($time), $week, $seq);
                    }
                }
            }
            if ($evening && $node->parent()->first_child()->innertext == "晚上") {
                $evening = FALSE;
                $children = $node->parent()->children();
                for ($i = 2; $i < count($children); ++$i) {
                    $content = trim($children[$i]->innertext);
                    $week = $i - 1;
                    $match;
                    $counter = preg_match_all('/(.*?)<br>(.*?)(\d+-\d+节)<br>(.*?)<br>/i', $content, $match);
                    for ($count = 0; $count < $counter; ++$count) {
                        $content = "{$match[1][$count]}{$match[2][$count]}{$match[3][$count]}\n{$match[4][$count]}\n";
                        $time = "晚上";
                        $seq = $count + 4;
                        $content = $this->formatHTML($content);
                        $this->addLesson(trim($content), trim($time), $week, $seq);
                    }
                }
            }
        }
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Utilities">
    private function getHtml($url, $post, $cookie) {
        /* echo "<pre>";
          echo "url: {$url}\n";
          echo "post: {$post}\n";
          echo "cookie: {$cookie}\n";
          echo "</pre>"; */
        $charset = "UTF-8";
        $res = Base::browser_request($url, NULL, $post);
        /* $res = Base::curl_request($url, $post, $cookie, [
          CURLOPT_HEADER => FALSE,
          CURLOPT_HEADERFUNCTION => function($ch, $header_line) use(&$charset) {
          // echo "<pre>=================================\n" . htmlspecialchars($header_line, ENT_IGNORE) . "</pre>";
          $matches = array();
          if (preg_match_all('/charset=(.*)/i', $header_line, $matches)) {
          $charset = trim(array_pop($matches[1]));
          // Log::addRuntimeLog("Charset {$charset} found.");
          }
          return strlen($header_line);
          },
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_AUTOREFERER => true,
          CURLOPT_MAXREDIRS => 5,
          CURLOPT_TIMEOUT => 15,
          CURLOPT_USERAGENT => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36"
          ]); */
        // Log::addRuntimeLog("Final charset is {$charset}.");
        if ($res === FALSE) {
            return FALSE;
        } else {
            return $res;
        }
    }

    /**
     * Remove elements like &lt;br&gt;, &lt;b&gt;.
     * 
     * @param String $rawHtmlText
     * @return String Formated text
     */
    private function formatHTML($rawHtmlText) {
        $res = preg_replace(['/<br>/', '/<.*>/'], ['\n'], $rawHtmlText);
        $res = html_entity_decode($res);
        return $res;
    }

    private function addLesson($content, $time, $week, $seq) {
        $this->Lessons[] = new Lesson($content, $time, $week, $seq);
    }

    public function toXML() {
        throw new Exception("", -1);
    }

    public function toJSON() {
        return json_encode($this);
    }

    // </editor-fold>
}

/* class Lesson {

  function __construct($name, $location, $teacher, $duration, $starttime) {
  $this->Name = $name;
  $this->Location = $location;
  $this->Teacher = $teacher;
  $this->Duration = $duration;
  $this->StartTime = $starttime;
  }

  public $Name = "";
  public $Location = "";
  public $Teacher = "";
  public $Duration = 0; // in seconds.
  public $StartTime = 0; // timestamp.

  } */

class Lesson {

    public $Content;
    public $Time;
    public $Week;
    public $Seq;

    function __construct($content, $time, $week, $seq) {
        $this->Content = $content;
        $this->Time = $time;
        $this->Week = $week;
        $this->Seq = $seq;
    }

}
