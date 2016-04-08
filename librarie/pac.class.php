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
 * Use this class to control and generate pac file.<br>
 * Pac file templates are placed under <i>librarie/pac_templates</i>.<br>
 * Pac file template is set by <i>setTemplate()</i>, default is <i>librarie/pac_templates/default.js</i>.<br>
 * Each template requires a set of arguments. Arguments are passed by <i>setTemplateArguments()</i>.<br>
 * These arguments are used to replace placeholder in the template file.<br>
 * Placeholders are comment strings looks like <b>/&#42;%arg<i>&lt;arg_number&gt;</i>% description &#42;/</b>.<br>
 * For example, the first three arguments passed into <i>setTemplateArguments()</i> will replace /&#42;%arg0%&#42;/, /&#42;%arg1%&#42;/, /&#42;%arg2%&#42;/ in order.<br>
 * You can also write descriptions in the placeholder label, jast keep the format of "%argn%" parts.
 * 
 *
 * @author master
 */
class pac {
    private $raw_pac_content = "";
    private $pac_content = "";
    private $base_path = "";
    private $default_template_name = 'default.js';
    
    public function __construct() {
        $this->base_path = ZPATH_CLASS_DIR . DIRECTORY_SEPARATOR . 'pac_templates';
        
        $this->setTemplate($this->default_template_name);
        $this->setTemplateArguments(
                'http.example.proxy:100',
                'https.example.proxy:200',
                base64_encode('["qq.com","mail.qq.com","bilibili.com","202.204.105.195"]'),
                base64_encode('["google.com","youtube.com","adobe.com","bing.com"]'));
        return;
    }
    public function setTemplate($template_name) {        
        $this->raw_pac_content = file_get_contents($this->base_path . DIRECTORY_SEPARATOR . $this->default_template_name);
    }
    public function setTemplateArguments($arguments) {
        $i = 0;
        $argc = func_num_args();
        $args = func_get_args();
        $regx = '/\/\*(.(?!\*\/))*?%argn%((?!\/\*).)*?\*\//i';
        $this->pac_content = $this->raw_pac_content;
        
        for ($i = 0; $i < $argc; ++$i) {
            $regx_i = str_replace('%argn%', "%arg{$i}%", $regx);
            $this->pac_content = preg_replace($regx_i, $args[$i], $this->pac_content);
        }
    }
    public function outputPAC() {
        header('Content-Type: application/x-ns-proxy-autoconfig');
        echo $this->pac_content;
        
    }
    
    public function debug_get_pac_content() {
        echo $this->pac_content;
        
    }
}
