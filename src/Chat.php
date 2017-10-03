<?php
namespace DarkChat;
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload


use DarkChat\Database;

class Chat extends Database
{
    protected $input;

    public function __construct() {
        parent::__construct();
        $this->input = $this->loadInput();
    }

    public static function load($name = '', $url='', $command='') {
        $instance = new self();
        $instance->setInstance($name);
        $command = $command=='' ? $instance->getInput('command') : $command;
        if ($url!='') $instance->setInput('self', $url);
        $instance->do($command);
        return $instance;
    }

    public function do($command = '') {
        switch ($command) {
            case 'xmlmessages':
                $this->displayMessagesXML();
                break;
            case 'xmlsendmessage':
                $this->sendMessageXML();
                break;
            case 'send':
                $this->sendHTMLMessage();
                break;
            default:
                $this->renderHTML();
                break;
        }
    }

    protected function loadInputVar($varname) {
        $data = '';
        if (array_key_exists($varname, $_GET)) $data = htmlspecialchars($_GET[$varname]);
        if (array_key_exists($varname, $_POST)) $data = htmlspecialchars($_POST[$varname]);
        return $data;
    }

    protected function loadServerVar($varname) {
        return $_SERVER[$varname];
    }

    protected function loadInput() {
        return array(
            'command' => $this->loadInputVar('hc'),
            'name' => $this->loadInputVar('sendname'),
            'message' => $this->loadInputVar('sendmessage'),
            'status' => '',
            'lastmod' => $this->loadInputVar('lmts'),
            'tzoffset' => $this->loadInputVar('tzoffset'),
            'addr' => $this->loadServerVar('REMOTE_ADDR'),
            'useragent' => $this->loadServerVar('HTTP_USER_AGENT'),
            'self' => $this->loadServerVar('PHP_SELF')
        );
    }

    public function getInput($name) {
        return $this->input[$name];
    }

    public function setInput($name, $value) {
        return $this->input[$name] = $value;
    }

    public function setInstance($name) {
        $this->setInput('instance', $name);
    }

    protected function getFormattedTime($time, $offset) {
        // times are stored in UTC
        $dateTime = new DateTime ($strtotime($time), new DateTimeZone('UTC'));
        // convert to users $offset time
        if ($offset) $dateTime->setTimezone(new DateTimeZone($offset < 0 ? $offset : '+' . $offset));
        // make pretty and return
        return $dateTime->format($this->getConfig('TIME_FORMAT'));
    }

    protected function getMessagesXML($timeoffset = 0) {
        $messages = $this->listDbMessages();
        $sXML = "<messagedata>\r\n";
        // a hash of the last message time is returned to know when rendering is acutally needed
        $sXML .= "    <messages lastmodified=\"" . $this->getFormattedTime($messages[0]["datetime"], $timeoffset) .
                "\" lmhash=\"" . md5($messages[0]["datetime"]) . "\">\r\n";
        // return all messages to display
        foreach ($messages as $message) {
            $sXML .= "        <message>\r\n";
            $sXML .= "            <datetime>" . $this->getFormattedTime($message["datetime"], $timeoffset) . "</datetime>\r\n";
            $sXML .= "            <sendername><![CDATA[" . stripslashes($message["name"]) . "]]></sendername>\r\n";
            $sXML .= "            <messagetext><![CDATA[" . stripslashes($message["message"]) . "]]></messagetext>\r\n";
            $sXML .= "        </message>\r\n";
        }
        $sXML .= "    </messages>\r\n";
        $sXML .= "</messagedata>\r\n";
        return $sXML;
    }

    protected function xmlHeaderNoCache() {
	header("Content-type: text/xml");
	header("Cache-Control: no-cache");
	header("Expires: -1");
        header("Pragma: no-cache");
    }

    protected function displayMessagesXML() {
        // Display messages as XML
        $this->xmlHeaderNoCache();
	$lmd = $this->getLastModifiedDate();
	header("Last-Modified: " . gmdate("r", strtotime($lmd)));
	if ($this->getInput('lastmod')!=md5($lmd)) {
	    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
	    echo $this->getMessagesXML($this->getInput('tzoffset'));
	    return (true);
	} else {
	    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
	    echo "<messagedata>\r\n";
	    echo "</messagedata>\r\n";
            return (true);
        }
    }

    protected function autoSendMessage() {
        return $this->sendMessage(
            $this->getInput('name'), $this->getInput('message'), 
            $this->getInput('addr'), $this->getInput('useragent')
        )!==false;
    }

    protected function sendMessageXML() {
	// Send message as and return response in XML
	if (($this->getInput('name')!='') && ($this->getInput('message')!='')) {
            if ($this->autoSendMessage()) {
                header("HTTP/1.1 201 Created");
                $this->xmlHeaderNoCache();
		echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
		echo "<status>received</status>";
	    } else {
		header("HTTP/1.1 500 Internal Server Error");
                $this->xmlHeaderNoCache();
		echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
		echo "<status>failed</status>";
	    }
	} else {
	    header("HTTP/1.1 400 Bad Request");
            $this->xmlHeaderNoCache();
	    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
	    echo "<status>invalid data</status>";
	}
    }

    protected function sendHTMLMessage() {
        if (($this->getInput('name')!='') && ($this->getInput('message')!='')) {
            if ($this->autoSendMessage()) {
		$this->setInput('status', 'Message sent');
            } else {
		$this->setInput('status', 'Message NOT sent');
            }
        } else {
	    $this->setInput('status', 'Message NOT sent');
        }
        $this->renderHTML();
    }

    protected function renderHTML() {
        $this->setInput('messages', $this->listDbMessages());
        include(__DIR__ . '/Web.php');
    }
}

