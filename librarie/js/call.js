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

// Global Ajax Request Template. Need initialization before use.
var _ajax_object_template = {
    // '/call.php?&ajax=1' is recommended.
    url: '/call.php',
    // data can also be an indexed array(NOT OBJECT) contains multiple function call.
    data: {
        // Variable name of an instance of class saved in session => 1 or 'session', class name with static method => 2 or 'static'.
        // REQUIRED when class name is specified.
        class_flag: 'session',
        // Class name or null when not required.
        class: null,
        // Function name or null when not required. When null, args will be ignored.
        func: null,
        // ARRAY(may be empty) contains required function. (Some function with single argument may want an array.)
        args: []
    }
};

// Global Ajax Request Porccessor. Use this function for Ajax communication.
function con(ajax_object, success_callback, error_callback) {
    // console.log('connect start');
    $.ajax({
        type: 'POST',
        dataType: "json",
        url: ajax_object.url,
        data: JSON.stringify(ajax_object.data),
        contentType: 'application/json; charset=utf-8',
        success: success_callback,
        error: error_callback
    });
    // console.log('Ajas Request: ' + ajax_object);
}

// Jast for quickly use. Call Server Function. The first three arguments are required.
function exec(func_name, args, success_callback, class_name, class_flag, error_callback) {
    // console.log('exec start');
    // console.log(arguments);
    var ajax_obj;
    if (func_name !== undefined && args !== undefined && success_callback !== undefined) {
        // console.log('construct ajax_obj');
        ajax_obj = {
            url: _ajax_object_template.url,
            data: {
                func: func_name,
                args: args
            }
        }
    } else {
        return false;
    }
    if (class_name) {
        if (class_flag) {
            ajax_obj.data.class = class_name;
            ajax_obj.data.class_flag = class_flag;
        } else {
            return false;
        }
    }
    // console.log('exec near end');
    con(ajax_obj, success_callback, error_callback);
}