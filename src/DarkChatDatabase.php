<?php

class DarkChatDatabase extends DarkChatConfig
{
    protected $db;

    public function __construct() {
            parent::__construct();
            $this->autoConfigure();
    }

    protected function initDb() {
        $this->db = new SQLite3(__DIR__ . $this->getConfig('DATABASE_LOCATION', SQLITE3_OPEN_READWRITE));
    }

    protected function createDb() {
        $db = new SQLite3(__DIR__ . $this->getConfig('DATABASE_LOCATION'));
    }

    protected function createDbTables() {
        $this->db->query(
            "CREATE TABLE messages (name varchar(255) NOT NULL default '', messagetext varchar(4096) NOT NULL default ''," .
            " modified_time datetime NOT NULL default '0000-00-00 00:00:00', modified_ip varchar(64) NOT NULL default ''," .
            " modified_agent varchar(255) NOT NULL default '', expired int(1) NOT NULL default 0 );"
        );
    }

    protected function autoConfigure()
    {
        // build the database if it doesn't exist
        if (!file_exists(__DIR__ . $this->getConfig('DATABASE_LOCATION'))) {
            $this->createDb();
            $this->initDb();
            $this->createDbTables();
        } else {
            $this->initDb();
        }
    }

    protected function dropDbTables() {
        $this->db->query("DROP TABLE messages;");
    }

    protected function listDbMessages() {
        $query = $this->db->query(
            "SELECT ROWID as id, name, messagetext AS message, modified_time AS datetime FROM messages" . 
            " WHERE expired=0 ORDER BY modified_time DESC LIMIT " . $this->getConfig('NUM_MESSAGES_DISPLAY') . ";"
        );
        $result = null;
        if ($query) 
        {
            $result = $query->fetchArray(SQLITE3_ASSOC);
        }
        return $result;
    }

    protected function getLastModifiedDate() {
        $result = $this->db->query(
            "SELECT modified_time FROM messages WHERE expired=0 ORDER BY modified_order DESC LIMIT 1;"
        );
        return $result->fetchSingle();
    }

    protected function removeOldMessages() {
        // automatically detect old messages and delete them, because disk space
        $result = $this->db->query("SELECT COUNT(*) FROM messages");
        if($result->fetchSingle() >= $this->getConfig('NUM_MESSAGES_KEEP')) {
            $this->db->query(
                "DELETE FROM messages WHERE modified_time < (SELECT modified_time FROM messages" .
                " ORDER BY modified_time DESC LIMIT 1 OFFSET " . ($this->getConfig('NUM_MESSAGES_KEEP') - 1) . ");"
            );
        }
    } 

    protected function dbEncodeTextMax($text, $max)
    {
        // keep a parameter under it's size limit but with intelligent sql encoding
        if (strlen($text)>$max) $text = substr($text, 0, $max);
        $encText = sqlite_escape_string($text);
        while (strlen($encText)>$max) {
            $text = substr($text, 0, -1);
            $encText = sqlite_escape_string($text);
        }
        return $encText;
    }

    protected function encodeMessage(&$name, &$messagetext, &$userip, &$useragent) {
        // keep sendDbMessage shorter by parsing data in encodeMessage
        $name = trim($name);
        $name = $this->dbEncodeTextMax($name, 255);

        $messagetext = trim($messagetext);
        $messagetext = $this->dbEncodeTextMax($messagetext, 4096);

        $userip = trim($userip);
        $userip = $this->dbEncodeTextMax($userip, 64);

        $useragent = trim($useragent);
        $useragent = $this->dbEncodeTextMax($useragent, 255);
    }

    protected function sendDbMessage($name, $messagetext, $userip, $useragent) {
        $this->encodeMessage($name, $messagetext, $userip, $useragent);
        if (
            (strlen($name)<=255) && (strlen($messagetext)<=4096) && (strlen($userip)<=64) && (strlen($useragent)<=255)
            && (strlen($name)>0) && (strlen($messagetext)>0) && (strlen($userip)>0) && (strlen($useragent)>0)
        ) {
            $this->removeOldMessages();
            if (
                $this->db->query("INSERT INTO messages VALUES ('" . $name . "', '". $messagetext . "', '" . gmdate("c") .
                "', '" . $userip . "', '" . $useragent . "', 0);")
            ) {
                return true;
            } else {
                // something went wrong
                return false;
            }
        } else {
            // invalid input
            return false;
        }
    }
}

