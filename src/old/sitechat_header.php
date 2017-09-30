<?php

// Quick PHP 5 Site Chat
//
// Chris Petersen 2007
//

// Setup instructions:
// 1. Change SITECHATSETUPMODE to TRUE below.
// 2. Deploy sitechat.php & sitechat_header.php on a web server running PHP.
// 3. Access sitechat.php via a web browser.
// 4. Change SITECHATSETUPMODE back to FALSE below.
// 5. Re-Deploy sitechat.php to the web server.
// 6. Access sitechat.php to use the application.


// PHP config

$sitechatembedded = false;
define("SITECHATSETUPMODE", false);
//define("SITECHATSETUPMODE", true);
define("SITECHATDATABASELOCATION", "../sitechat.db");
define("SITECHATNUMMESSAGESDISPLAY", 20);
define("SITECHATNUMMESSAGESKEEP", 50);
define("SITECHATUIMESSAGESREFRESHDELAY", 5000);
define("SITECHATUISTATUSSHOWTIME", 2500);

// PHP 4 & 5 Library

class sitechatDatabaseAdmin {

	private $db;

	function __construct() {
		$sqliteerror = "";
		if (class_exists('SQLite3'))
		{
			$this->db = new SQLite3(SITECHATDATABASELOCATION, 0666);
		}
		else
		{
			$this->db = new SQLiteDatabase(SITECHATDATABASELOCATION, 0666, $sqliteerror);
		}
		//$this->db = new SQLite3(SITECHATDATABASELOCATION, 0666, $sqliteerror);
	}

	public function createDbTables() {
		//$this->db->query( "DROP TABLE messages" );
		$this->db->query( "CREATE TABLE messages (name varchar(255) NOT NULL default '', messagetext varchar(4096) NOT NULL default '', modified_time datetime NOT NULL default '0000-00-00 00:00:00', modified_ip varchar(64) NOT NULL default '', modified_agent varchar(255) NOT NULL default '', expired int(1) NOT NULL default 0 )" );
	}

}

class sitechatDatabase {

	private $db;

	function __construct() {
		$sqliteerror = "";
		if (class_exists('SQLite3'))
		{
			$this->db = new SQLite3(SITECHATDATABASELOCATION, 0666);
		}
		else
		{
			$this->db = new SQLiteDatabase(SITECHATDATABASELOCATION, 0666, $sqliteerror);
		}
		//$this->db = new SQLite3(SITECHATDATABASELOCATION, 0666, $sqliteerror);
	}

	public function listMessages() {
		$query = $this->db->query("SELECT ROWID as id, name, messagetext AS message, modified_time AS datetime FROM messages WHERE expired=0 ORDER BY modified_time DESC LIMIT " . SITECHATNUMMESSAGESDISPLAY);
		$result = null;
		if ($query) 
		{
			$result = $query->fetchAll(SQLITE_ASSOC);
		}
		return $result;
	}

	public function sendMessage($name, $messagetext, $userip, $useragent ) {
		$name = trim($name);
		if (strlen($name)>255) $name = substr($name, 0, 255);
		$sName = sqlite_escape_string( $name );
		while (strlen($sName)>255) {
			$name = substr($name, 0, -1);
			$sName = sqlite_escape_string( $name );
		}

		$messagetext = trim($messagetext);
		if (strlen($messagetext)>4096) $messagetext = substr($messagetext, 0, 4096);
		$sMessageText = sqlite_escape_string( $messagetext );
		while (strlen($sMessageText)>4096) {
			$messagetext = substr($messagetext, 0, -1);
			$sMessageText = sqlite_escape_string( $messagetext );
		}

		$userip = trim($userip);
		if (strlen($userip)>64) $userip = substr($userip, 0, 64);
		$sUIP = sqlite_escape_string( $userip );
		while (strlen($sUIP)>64) {
			$userip = substr($userip, 0, -1);
			$sUIP = sqlite_escape_string( $userip );
		}

		$useragent = trim($useragent);
		if (strlen($useragent)>255) $useragent = substr($useragent, 0, 255);
		$sUAgent = sqlite_escape_string( $useragent );
		while (strlen($sUAgent)>255) {
			$useragent = substr($useragent, 0, -1);
			$sUAgent = sqlite_escape_string( $useragent );
		}

		if ((strlen($sName)<=255)&&(strlen($sMessageText)<=4096)&&(strlen($sUIP)<=64)&&(strlen($sUAgent)<=255)&&(strlen($sName)>0)&&(strlen($sMessageText)>0)&&(strlen($sUIP)>0)&&(strlen($sUAgent)>0)) {
			// check if records need to be deleted
			$qr = $this->db->query( "SELECT COUNT(*) FROM messages" );
			if( $qr->fetchSingle() >= SITECHATNUMMESSAGESKEEP ) {
				// delete obselete records
				$this->db->query( "DELETE FROM messages WHERE modified_time < (SELECT modified_time FROM messages ORDER BY modified_time DESC LIMIT 1 OFFSET " . (SITECHATNUMMESSAGESKEEP - 1) . "  );" );
			}
			// add new message
			if ($this->db->query( "INSERT INTO messages VALUES ('" . $sName . "', '". $sMessageText . "', '" . date("c") . "', '" . $sUIP . "', '" . $sUAgent . "', 0);" )) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function getMessagesXML() {
		$messages = $this->listMessages();
		$sXML = "<messagedata>\r\n";
		$sXML .= "    <messages lastmodified=\"" . date("r", strtotime($messages[0]["datetime"])) . "\" lmhash=\"" . md5($messages[0]["datetime"]) . "\">\r\n";
		foreach ($messages as $message) {
			$sXML .= "        <message>\r\n";
			$sXML .= "            <datetime>" . date("r", strtotime($message["datetime"])) . "</datetime>\r\n";
			$sXML .= "            <sendername><![CDATA[" . stripslashes($message["name"]) . "]]></sendername>\r\n";
			$sXML .= "            <messagetext><![CDATA[" . stripslashes($message["message"]) . "]]></messagetext>\r\n";
			$sXML .= "        </message>\r\n";
		}
		$sXML .= "    </messages>\r\n";
		$sXML .= "</messagedata>\r\n";
		return $sXML;
	}

	function getLastModifiedDate() {
		$messages = $this->listMessages();
		return $messages[0]["datetime"];
	}
}


// PHP Init

if (SITECHATSETUPMODE) {
	$admin = new sitechatDatabaseAdmin();
	$admin->createDbTables();
}

$chat = new sitechatDatabase();

// Decode HTTP Input

$command = "";
if (array_key_exists("hc", $_GET)) $command = htmlspecialchars($_GET["hc"]);
if (array_key_exists("hc", $_POST)) $command = htmlspecialchars($_POST["hc"]);
$getname = "";
if (array_key_exists("sendname", $_GET)) $getname = htmlspecialchars($_GET["sendname"]);
if (array_key_exists("sendname", $_POST)) $getname = htmlspecialchars($_POST["sendname"]);
$getmessage = "";
if (array_key_exists("sendmessage", $_GET)) $getmessage = htmlspecialchars($_GET["sendmessage"]);
if (array_key_exists("sendmessage", $_POST)) $getmessage = htmlspecialchars($_POST["sendmessage"]);
$getlastmod = "";
if (array_key_exists("lmts", $_GET)) $getlastmod = htmlspecialchars($_GET["lmts"]);
if (array_key_exists("lmts", $_POST)) $getlastmod = htmlspecialchars($_POST["lmts"]);

// XML UI

if ($command=="xmlmessages") {
	// Display messages as XML
	header("Content-type: text/xml");
	$lmd = $chat->getLastModifiedDate();
	header("Last-Modified: " . date("r", strtotime($lmd)));
	// Fix MSIE
	header("Cache-Control: no-cache");
	header("Expires: -1");
	header("Pragma: no-cache");
	if ($getlastmod!=md5($lmd)) {
		echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
		echo $chat->getMessagesXML();
		return (true);
	} else {
		echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
		echo "<messagedata>\r\n";
		echo "</messagedata>\r\n";
		return (true);
	}

} elseif ($command=="xmlsendmessage") {
	// Send message as XML
	if (($getname!="")&&($getmessage!="")) {
		if($chat->sendMessage($getname, $getmessage, $_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"])!==false) {
			header("HTTP/1.1 201 Created");
			header("Content-type: text/xml");
			header("Cache-Control: no-cache");
			header("Expires: -1");
			header("Pragma: no-cache");
			echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
			echo "<status>received</status>";
		} else {
			header("HTTP/1.1 500 Internal Server Error");
			header("Content-type: text/xml");
			header("Cache-Control: no-cache");
			header("Expires: -1");
			header("Pragma: no-cache");
			echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
			echo "<status>failed</status>";
		}
	} else {
		header("HTTP/1.1 400 Bad Request");
		header("Content-type: text/xml");
		header("Cache-Control: no-cache");
		header("Expires: -1");
		header("Pragma: no-cache");
		echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
		echo "<status>invalid data</status>";
	}
	return (true);
}

// HTML UI

// Send message

$messagestatus = "";

if (($command=="send")&&($getname!="")&&($getmessage!="")) {
	if($chat->sendMessage($getname, $getmessage, $_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"])!==false) {
		$messagestatus = "Message sent";
	} else {
		$messagestatus = "Message NOT sent";
	}
}

// Get messages

$messages = $chat->listMessages();

return (false);

?>
