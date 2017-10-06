<?php
namespace peeto\DarkChat;


use peeto\DarkChat\Database;

/**
 * Chat class
 * 
 * Invokes the core functionality
 * 
 * try Chat:load();
 */
class Chat extends Database
{
    protected $input;

    public function __construct($config = '') {
        parent::__construct($config);
        $this->input = $this->loadDefaultInput();
        $this->loadDefaultXMLRoutes();
        $this->overloadRoutesFromConfig();
    }

    /**
     * Helper/factory for loading DarkChat
     * 
     * see loadUserInput() to override input options
     * otherwise loadDefaultInput() loads options
     * from the web server
     * 
     * @param array $input
     * @return \self
     */
    public static function load($input) {
        $config = '';
        if(isset($input['config'])) {
            // set the location of the configuration file
            $config = $input['config'];
        }
        $instance = new self($config);
        $instance->loadUserInput($input);
        $instance->go();
        return $instance;
    }

    /**
     * go() runs DarkChat
     * automatically called from load()
     */
    public function go() {
        switch ($this->getInput('command')) {
            case 'xmlmessages':
                $this->displayMessagesXML();
                break;
            case 'xmlsendmessage':
                $this->sendMessageXML();
                break;
            case 'send':
                $this->sendHTMLMessage();
                break;
            case 'sendhtml':
                $this->sendHTMLMessageOnly();
                break;
            default:
                $this->renderHTML();
                break;
        }
    }

    public function getInput($name) {
        if (isset($this->input[$name])) {
            return $this->input[$name];
        } else {
            return '';
        }
    }

    public function setInput($name, $value) {
        return $this->input[$name] = $value;
    }

    /**
     * Allows HTML and Javascript to be prefixed with a name
     * so multiple DakChat instances can run on the same web page
     * (optional)
     * 
     * @param string $name
     */
    public function setInstance($name) {
        $this->setInput('instance', $name);
    }

    protected function loadInputVar(string $varname) {
        $data = '';
        if (array_key_exists($varname, $_GET)) {
            $data = htmlspecialchars($_GET[$varname]);
        }
        if (array_key_exists($varname, $_POST)) {
            $data = htmlspecialchars($_POST[$varname]);
        }
        return $data;
    }

    protected function loadServerVar(string $varname) {
        return $_SERVER[$varname];
    }

    protected function loadDefaultInput() {
        return [
            'command' => $this->loadInputVar('hc'),
            'name' => $this->loadInputVar('sendname'),
            'message' => $this->loadInputVar('sendmessage'),
            'lastmod' => $this->loadInputVar('lmts'),
            'tzoffset' => $this->loadInputVar('tzoffset'),
            'addr' => $this->loadServerVar('REMOTE_ADDR'),
            'useragent' => $this->loadServerVar('HTTP_USER_AGENT'),
            'route' => $this->loadServerVar('PHP_SELF'),
            'status' => '',
            'messages' => false
        ];
    }
    
    protected function loadDefaultXMLRoutes() {
        $this->setInput('xml_message_route', $this->getInput('route'));
        $this->setInput('xml_send_message_route', $this->getInput('route'));
    }
    
    protected function overloadRoutesFromConfig() {
        if ($this->getConfig('DEFAULT_ROUTE') != '') {
            $this->setInput('route', $this->getConfig('DEFAULT_ROUTE'));
        }
        
        if ($this->getConfig('XML_MESSAGE_ROUTE') != '') {
            $this->setInput(
                'xml_message_route', 
                $this->getConfig('XML_MESSAGE_ROUTE')
            );
        }
        
        if ($this->getConfig('XML_SEND_MESSAGE_ROUTE') != '') {
            $this->setInput(
                'xml_send_message_route', 
                $this->getConfig('XML_SEND_MESSAGE_ROUTE')
            );
        }
        
    }
    
    protected function loadUserInput($input) {
        if (is_array($input)) {
            if(isset($input['name'])) {
                // set the "instance" name
                $this->setInstance($input['name']);
            }
            if(isset($input['route'])) {
                // change the default url
                $this->setInput('route', $input['route']);
            }
            if(isset($input['xml_message_route'])) {
                // change where messages ae loaded from in XML 
                $this->setInput('xml_message_route', 
                    $input['xml_message_route']);
            }
            if(isset($input['xml_send_message_route'])) {
                // change where message are sent to in XML
                $this->setInput('xml_send_message_route', 
                    $input['xml_send_message_route']);
            }            
            if(isset($input['command'])) {
                // change the route, which command DarkChat will perform
                $this->setInput('command', $input['command']);
            }
        }
    }

    protected function getFormattedTime($time) {
        // times are stored in UTC
        $dateTime = new \DateTime ($time, new \DateTimeZone('UTC'));
        // convert to users $offset time
        $offset = $this->getInput('tzoffset');
        if ($offset) 
        {
            $dateTime->setTimezone(
                new \DateTimeZone(
                    $offset < 0 ? $offset : '+' . $offset
                )
            );
        }
        // make pretty and return
        return $dateTime->format($this->getConfig('TIME_FORMAT'));
    }

    protected function getMessagesXML() {
        $messages = $this->listDbMessages();
        $sXML = "<messagedata>\r\n";
        // a hash of the last message time is returned to know
        //  when rendering is acutally needed
        $sXML .= "    <messages lastmodified=\"" .
            $this->getFormattedTime($messages[0]["date_time"])
             . "\" lmhash=\"" . md5($messages[0]["date_time"]) . "\">\r\n";
        // return all messages to display
        foreach ($messages as $message) {
            $sXML .= "        <message>\r\n";
            $sXML .= "            <date_time>" . 
                $this->getFormattedTime($message["date_time"]) .
                "</date_time>\r\n";
            $sXML .= "            <sendername><![CDATA[" . 
                stripslashes($message["name"]) . "]]></sendername>\r\n";
            $sXML .= "            <messagetext><![CDATA[" . 
                stripslashes($message["message"]) . "]]></messagetext>\r\n";
            $sXML .= "        </message>\r\n";
        }
        $sXML .= "    </messages>\r\n";
        $sXML .= "</messagedata>\r\n";
        return $sXML;
    }

    protected function getMessagesHTML() {
        $messages = $this->listDbMessages();
        $webmessages = false;

        foreach ($messages as $message) {
            $newmessage = $message;
            $newmessage['date_time'] = 
                $this->getFormattedTime($message['date_time']);
            $webmessages[] = $newmessage;
        }
        return $webmessages;
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
            echo $this->getMessagesXML();
            return (true);
        } else {
            echo "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\r\n";
            echo "<messagedata>\r\n";
            echo "</messagedata>\r\n";
            return (true);
        }
    }

    protected function autoSendMessage() {
        return $this->sendDbMessage(
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

    protected function sendHTMLMessageOnly() {
        if (
            ($this->getInput('name')!='')
            && ($this->getInput('message')!='')
        ) {
            if ($this->autoSendMessage()) {
                $this->setInput('status', 'Message sent');
            } else {
                $this->setInput('status', 'Message NOT sent');
            }
        } else {
            $this->setInput('status', 'Message NOT sent');
        }
    }
    
    protected function sendHTMLMessage() {
        $this->sendHTMLMessageOnly();
        $this->renderHTML();
    }

    /**
     * Renders the web page, see Web.php
     */
    protected function renderHTML() {
        $this->setInput('messages', $this->getMessagesHTML());
        include $this->getConfig('UI_LOCATION');
    }
}

