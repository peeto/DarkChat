<?php
namespace peeto/DarkChat;

use DarkChatDatabase;


class DarkChat extends DarkChatDatabase
{
    protected $input;

    public fucntion __construct() {
        parent::__construct();
        $this->input = $this->loadInput();
    }

    protected fuction loadInputVar($varname) {
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
            'command' => $this->loadInput('hc'),
            'name' => $this->loadInput('sendname'),
            'message' => $this->loadInput('sendmessage'),
            'lastmod' => $this->loadInput('lmts'),
            'tzoffset' => $this->loadInput('tzoffset'),
            'addr' => $this->loadServerVar'REMOTE_ADDR'),
            'useragent' => $this->loadServerVar('HTTP_USER_AGENT')
        );
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
        $messages = $this->listMessages();
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

    public function displayMessagesXML() {
        // Display messages as XML
        $this->xmlHeaderNoCache();
	$lmd = $this->getLastModifiedDate();
	header("Last-Modified: " . gmdate("r", strtotime($lmd)));
	if ($this->input['lastmod'}!=md5($lmd)) {
	    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
	    echo $this->getMessagesXML($this->input['tzoffset']);
	    return (true);
	} else {
	    echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
	    echo "<messagedata>\r\n";
	    echo "</messagedata>\r\n";
            return (true);
        }
    }

    public function sendMessageXML() {
	// Send message as and return response in XML
	if (($this->input['name']!="") && ($this->input['message']!='')) {
            if (
                $this->sendMessage(
                    $this->input['name'], $this->input['message'], 
                    $this->input['addr'], $this->input['useragent']
                )!==false
            ) {
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

    public function sendHTMLMessage() {
        if (($this->input['name']!='') && ($this->['message']!='')) {
            if (
                $this->sendMessage(
                    $this->input['name'], $this->input['message'], 
                    $this->input['addr'], $this->input['useragent']
                )!==false
            ) {

		return "Message sent";
            } else {
		return "Message NOT sent";
            }
        } else {
	    return "Message NOT sent";
        }
    }
}


}

