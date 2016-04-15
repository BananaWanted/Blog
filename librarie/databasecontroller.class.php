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
 * hold and manage the database connection, 
 * and process the SQL query
 */
class DatabaseController {

//<editor-fold desc="constructors">
    private $connection = null;
    private $prefix = "";

    function __construct() {
        $this->__constuct(DB_ADDRESS, DB_NAME, DB_USERNAME, DB_PASSWORD, DB_PREFIX);
    }

    private function __constuct($host, $dbName, $username, $password, $prefix) {
        $this->connect($host, $dbName, $username, $password);
        $this->prefix = $prefix;
    }

    /**
     * Connect to a specified database than the default database;
     * @param string $host
     * @param string $dbName
     * @param string $username
     * @param string $password
     */
    private function connect($host, $dbName, $username, $password) {
        try {
            $connectionString = sprintf('mysql:host=%s;dbname=%s', $host, $dbName);
            $this->connection = new PDO($connectionString, $username, $password);
        } catch (PDOException $pe) {
            Log::addErrorLog($pe->getMessage());
            die();
        }
    }
//</editor-fold>
    
// <editor-fold desc="Dangerous Methods.">
    public function exec($sql) {
        $result = $this->connection->exec($sql);
        return $result;
    }

    /**
     * 
     * quick query, pass sql query and fetch mode in one line.
     * @param string $sql
     * @param int $fetchMode [Optional] default is PDO::FETCH_ASSOC
     * @return PDOStatement
     */
    public function query($sql, $fetchMode) {
        $q = $this->connection->query($sql);
        if ($q === FALSE) {
            Log::addErrorLog("Can not query from database, the query statement is \"{$sql}\"");
            //die();
            return FALSE;
        }
        if (func_num_args() > 1) {
            $fetchMode = PDO::FETCH_ASSOC;
        }
        $q->setFetchMode($fetchMode);
        return $q;
    }
// </editor-fold>
    
    /**
     * Prepare a PDOStatement for fetch selection.
     * 
     * @param string $table table name without prefix
     * @param array $columns array of column names.
     * @param string $where <i>[Optional]</i> without word "WHERE"
     * @return PDOStatement
     * 
     * @example $query = fetchSelection("config, array("c1", "c2"), "id>10"); $row = $query->fetch();
     */
    public function select($table, array $columns, $where, $others) {
        $columnField = "";
        $whereField = "";
        $sql = <<<EOSQL
                SELECT %s
                FROM {$this->prefix}%s
                %s
EOSQL;
        foreach ($columns as $index => $name) {
            $columnField .= $name;
            if ($index < count($columns) - 1) {
                $columnField .= ', ';
            }
        }
        if (isset($where)) {
            $whereField = 'where ' . $where;
        }

        $sql = sprintf($sql, $columnField, $table, $whereField);
        if (isset($others)) {
            $sql .= ' ' . $others;
        }

        //Log::addRuntimeLog("fetch selection: $sql");
        $query = $this->query($sql);
        return $query;
    }

    /**
     * Execute an update statements. You should add "" or other functions yourself.
     * @param string $table table name without prefix
     * @param array $set column=>value pairs.
     * @param string $where where statement without the word "where"
     * @param string $others
     * @return int number of effected rows, or FALSE on faliure.
     */
    public function update($table, array $set, $where, $others) {
        $sql = 'UPDATE %s SET %s where %s %s';
        $tableF = $this->prefix . $table;
        $setF = "";
        foreach ($set as $name => $value) {
            $setF .= $name . '=' . $value . ',';
        }
        $setF = substr($setF, 0, strlen($setF) - 1);
        $setF .= " ";
        $whereF = 'where ' . $where;
        $othersF = $others;

        $sql = sprintf($sql, $tableF, $setF, $whereF, $othersF);
        return $this->exec($sql);
    }

    /**
     * 
     * @param string $table Table name without prefix.
     * @param string $where Where statement without word "where".
     */
    public function delete($table, $where) {
        $sql = 'DELETE FROM %s WHERE %s';
        $sql = sprintf($sql, $this->prefix . $table, $where);
        return $this->exec($sql);
    }

    /**
     * 
     * @param string $table Table name without prefix.
     * @param array|string $columns note: when they column is an associative array as $column_name=>$value, the $values array will not be used.
     * @param array|string $values
     * @return mixed Integer number of effected rows. Or FALSE.
     */
    public function insert($table, $columns, $values) {
        $tableF = "";
        $columnF = "";
        $valueF = "";
        $sql = 'INSERT INTO %s (%s) VALUES (%s)';

        $tableF = $this->prefix . $table;
        if (is_string($columns) && is_string($values)) {
            $columnF = $columns;
            $valueF = $values;
        } elseif (is_array($columns) && is_associative_array($columns)) {
            foreach ($columns as $name => $value) {
                $columnF .= $name . ',';
                $valueF .= $value . ',';
            }
            $columnF = substr($columnF, 0, strlen($columnF) - 1);
            $valueF = substr($valueF, 0, strlen($valueF) - 1);
        } elseif (is_array($columns) && is_array($values) && !is_associative_array($values)) {
            if (count($columns) !== count($values)) {
                return FALSE;
            }
            $count = count($columns);
            for ($i = 0; $i < $count; $i++) {
                $columnF .= $columns[i] . ',';
                $valueF .= $values[i] . ',';
            }
            $columnF = substr($columnF, 0, strlen($columnF) - 1);
            $valueF = substr($valueF, 0, strlen($valueF) - 1);
        } else {
            return FALSE;
        }
        $sql = sprintf($sql, $tableF, $columnF, $valueF);

        return $this->exec($sql);
    }

    /**
     * 
     * @param string $name Table name without prefix.
     * @param string|array $columns A string in th statement, or an Array of name=><b>options</b> pairs.<br><br>
     * <b>options</b>:<i>mixed type</i><br>
     * <ul>
     * <li>string:  empty or option string.</li>
     * <li>array:  normal array contains options, or empty. Example: {{"NOT NULL", "PRIMARY KEY"}, ...}</li>
     * <li>options could be following words: data_type[size] [NOT NULL|NULL] [DEFAULT value] [AUTO_INCREMENT]</li>
     * </ul>
     */
    public function createTable($name, $columns, $ifNotExists = TRUE, $others = "") {
        $sql = 'CREATE TABLE %s %s(%s) %s'; // $existF, $tableF, $columnF, $others
        $existF = $ifNotExists ? "IF NOT EXISTS" : "";
        $tableF = $this->prefix . $name;
        $columnF = "";

        if (is_string($columns)) {
            $columnF = $columns;
        } elseif (is_array($columns)) {
            foreach ($columns as $name => $value) {
                $row = $name . ' ';
                if (is_string($value)) {
                    $row .= $value;
                } elseif (is_array($value)) {
                    foreach ($value as $option) {
                        if (is_string($option)) {
                            $row .= $option . " ";
                        } else {
                            Log::addErrorLog('SQL Create Table Error: Option is not a string');
                            return FALSE;
                        }
                    }
                } else {
                    Log::addErrorLog('SQL Create Table Error: Invalid type of parm $column');
                    return FALSE;
                }
                $columnF .= $row . ',';
            }
            $columnF = substr($columnF, 0, strlen($columnF) - 1);
        } else {
            return FALSE;
        }
        $sql = sprintf($sql, $existF, $tableF, $columnF, $others);

        return $this->exec($sql);
    }

    public function addReceivedMessage(WechatMessage $message) {
        if (!isset($message->MsgId)) {
            return;
        }
        $prefix = DB_PREFIX;
        $safeXML = mysql_escape_string($message->toXML());
        $sql = <<<EOSQL
            INSERT INTO {$prefix}received_message
            (message_id, to_user_name, from_user_name, create_time, message_type, whole_xml_pac)
            value (
            {$message->MsgId},
            "{$message->ToUserName}",
            "{$message->FromUserName}",
            FROM_UNIXTIME({$message->CreateTime}),
            "{$message->MsgType}",
            "{$safeXML}"
                );
EOSQL;
        $row = $this->connection->exec($sql);
        if ($row === FALSE) {
            Log::addErrorLog("add received message, updata database failed. SQL: $sql");
            die;
        } else {
            Log::addRuntimeLog("received new message, insert into database successful");
        }
    }

    public function isMessageExists(WechatMessage $message) {
        if (!isset($message->MsgId)) {
            return FALSE;
        }
        $prefix = DB_PREFIX;
        $sql = <<<EOSQL
                SELECT message_id
                FROM {$prefix}received_message
                WHERE message_id = {$message->MsgId};
EOSQL;
        $query = $this->query($sql);
        $row = $query->fetch();
        if ($row === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * 
     * @param WechatMessage $receivedMessage a subset of WechatMessage's <b>Special Property Set</b>.
     * @param WechatMessage $replyMessage a subset of WechatMessage's <b>Special Property Set</b>.
     */
    public function setAutoReply(WechatMessage $receivedMessage, WechatMessage $replyMessage) {
        $prefix = DB_PREFIX;
        $task = array(
            ':receivedMsgType' => $receivedMessage->MsgType,
            ':replyMsgType' => $replyMessage->MsgType,
            ':receivedMessageXML' => $receivedMessage->toXML(),
            ':replyMessageXML' => $replyMessage->toXML()
        );
        $sql = "INSERT INTO {$prefix}auto_reply
                (receive_message_type, reply_message_type, keyword_in_xml, reply_in_xml)
                values(:receivedMsgType, :replyMsgType,
                    :receivedMessageXML, :replyMessageXML)";
        $exec = $this->connection->prepare($sql);
        if ($exec === false) {
            Log::addErrorLog("database set auto reply, prepare statement failed.");
            die;
            exit;
        }
        if (!$exec->execute($task)) {
            Log::addErrorLog('database set auto reply, execute statement failed.');
            die;
            exit;
        }
    }

    /**
     * 
     * @param WechatMessage $receivedMessage[Optional] A WechatMessage Object. <b>Special Property Set</b> Properties will be checked.
     * This function will query all rows matchs the $receivedMessage's Properties. Default will return all row in database.
     * when the $receivedMessage is specified, the property "MsgType" is required.
     * @return mixed An Array of WechatMessage, which contains a subset of WechatMessage's <b>Special Property Set</b>. Returns FALSE if none maches.
     */
    public function getAutoReply(WechatMessage $receivedMessage) {
        $result = new WechatMessage(array('MsgType' => 'text'));
        $matchArray = array();
        $prefix = DB_PREFIX;
        $sql = <<<EOSQL
                SELECT keyword_in_xml, reply_in_xml
                FROM {$prefix}auto_reply
                WHERE receive_message_type="%s";
EOSQL;
        $q = $this->query(sprintf($sql, $receivedMessage->MsgType));
        while ($row = $q->fetch()) {
            $keywordMsg = WechatAPI::receiveMessage($row['keyword_in_xml']);
            $valid = FALSE;
            switch ($receivedMessage->MsgType) {
                case 'text':
                    $valid = trim($keywordMsg->Content) === trim($receivedMessage->Content);
                    /* Log::addRuntimeLog("get auto reply: ".$keywordMsg->Content."=>".$receivedMessage->Content).PHP_EOL;
                      Log::addRuntimeLog("get auto reply trimed: ".trim($keywordMsg->Content)."=>".trim($receivedMessage->Content)).PHP_EOL;
                      $validValue = $valid ? 'True' : 'False';
                      Log::addRuntimeLog("valied: ".$validValue); */
                    break;
                case 'event':
                    $valid = strtoupper($keywordMsg->Event) == strtoupper($receivedMessage->Event) && trim($keywordMsg->EventKey) == trim($receivedMessage->EventKey);
                    break;
                default:
                    $cleanMsg = $receivedMessage->trim();
                    $result->Content = "message type not supported.\ndetail:\n{$cleanMsg->toString()}";
                    return $result;
                    break;
            }
            if ($valid) {
                $matchArray[] = $row['reply_in_xml'];
            }
        }
        if (count($matchArray) > 0) {
            //shuffle($matchArray);
            return WechatAPI::receiveMessage($matchArray[array_rand($matchArray)]);
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param array $receivedType <i>[Optional]</i> default select all received type.
     * @param array $replyType <i>[Optional]</i> default select all reply type.
     * 
     * @return array an array of array(id, WechatMessage key, WechatMessage value).
     */
    public function getAutoReplySet(array $receivedType, array $replyType) {
        $where = "";
        if (isset($receivedType)) {
            $where .= "(";
            foreach ($receivedType as $index => $type) {
                $where .= "receive_message_type='$type' ";
                if ($index < count($receivedType) - 1) {
                    $where .= "OR ";
                }
            }
            $where .= ") ";
        }
        if (isset($replyType)) {
            if (isset($receivedType)) {
                $where .= "AND (";
            } else {
                $where .= "(";
            }
            foreach ($replyType as $index => $type) {
                $where .= "reply_message_type='$type' ";
                if ($index < count($replyType) - 1) {
                    $where .= "OR ";
                }
            }
            $where .= ") ";
        }
        //echo $where;
        $query = $this->select("auto_reply", array("id", "keyword_in_xml", "reply_in_xml"), $where, 'order by id');
        $resultSet = array();
        while ($row = $query->fetch()) {
            $resultSet[] = array(
                $row['id'],
                WechatAPI::receiveMessage($row['keyword_in_xml']),
                WechatAPI::receiveMessage($row['reply_in_xml'])
            );
        }

        return $resultSet;
    }

    public function editAutoReply($id, WechatMessage $key, WechatMessage $reply) {
        $keyT = $key->trim();
        $replyT = $reply->trim();

        $table = 'auto_reply';
        $set = array(
            'receive_message_type' => '"' . $keyT->MsgType . '"',
            'reply_message_type' => '"' . $replyT->MsgType . '"',
            'keyword_in_xml' => '"' . $keyT->toXML() . '"',
            'reply_in_xml' => '"' . $replyT->toXML() . '"'
        );
        $where = 'id=' . $id;

        return $this->update($table, $set, $where, "");
    }

}
