<?php
/**
 * This file is used by the Chat class for rendering HTML
 */

$iname = $this->getInput('instance');
?>
<script language="javascript" type="text/javascript">
<!--

function getXMLHTTPRequestObject() {
    // function to return a *working* XMLHTTPRequest object
    var obj = false;
    if (window.XMLHttpRequest && !(window.ActiveXObject)) {
        try {
            obj = new XMLHttpRequest();
        } catch (e) {
            obj = false;
        }
    } else if (window.ActiveXObject) {
        try {
            obj = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            obj = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            obj = false;
        }
        }
    }
    return obj;
}

String.prototype.trim = function() { return this.replace(/^\s+|\s+$/g, ""); };

var <?php echo $iname; ?>loadMessagesXHRO;
var <?php echo $iname; ?>sendMessageXHRO;
var <?php echo $iname; ?>oLoadMessagesTimer = "";
var <?php echo $iname; ?>oSendStatusTimer = "";
var <?php echo $iname; ?>sLastMessageHash = "";

function <?php echo $iname; ?>loadMessagesXHROHandler() {
    var oMessageStatusUI = document.getElementById("<?php echo $iname; ?>sendstatus");
    if (<?php echo $iname; ?>loadMessagesXHRO.readyState == 3) {
        oMessageStatusUI.innerHTML = "Retrieving new messages...";
    }
    if (<?php echo $iname; ?>loadMessagesXHRO.readyState == 4) {
        if (<?php echo $iname; ?>loadMessagesXHRO.status == 200) {
            var oMessageUI = document.getElementById("<?php echo $iname; ?>messages");
            var oXML = <?php echo $iname; ?>loadMessagesXHRO.responseXML;
            var oXMLHeader = oXML.getElementsByTagName("messages");
            if (oXMLHeader[0]!=undefined) {
                sLastMessageHash = oXMLHeader[0].getAttribute("lmhash");
                var oXMLData = oXML.getElementsByTagName("message");
                var sHTML = "";
                for (var i = 0; i < oXMLData.length; i++) {
                    sHTML += "<div class=\"messageblock\">\r\n";
                    sHTML += "  <div class=\"header\">\r\n";
                    sHTML += "      <div class=\"date_time\">" + oXMLData[i].getElementsByTagName("date_time")[0].childNodes[0].data + "</div>\r\n";
                    sHTML += "      <div class=\"username\"><b>" + oXMLData[i].getElementsByTagName("sendername")[0].childNodes[0].data +
                        "</b> <i>said...</i></div>\r\n";
                    sHTML += "  </div>\r\n";
                    sHTML += "  <div class=\"messagetext\">" + oXMLData[i].getElementsByTagName("messagetext")[0].childNodes[0].data + "</div>\r\n";
                    sHTML += "</div>\r\n\r\n";
                }
                if (sHTML!="") {
                    oMessageUI.innerHTML = sHTML;
                    oMessageStatusUI.innerHTML = "";
                } else {
                    oMessageStatusUI.innerHTML = "Failed to retrieve new messages.";
                }
            }
        } else {
            oMessageStatusUI.innerHTML = "Failed to retrieve new messages.";
        }
    }
}

function <?php echo $iname; ?>sendMessageXHROHandler() {
    var oMessageStatusUI = document.getElementById("<?php echo $iname; ?>sendstatus");
    if (<?php echo $iname; ?>sendMessageXHRO.readyState == 3) {
        oMessageStatusUI.innerHTML = "Sending message...";
    }
    if (<?php echo $iname; ?>sendMessageXHRO.readyState == 4) {
        var oMessageSendButtonUI = document.getElementById("<?php echo $iname; ?>sendsubmit");
        var oMessageResetButtonUI = document.getElementById("<?php echo $iname; ?>sendreset");
        var oXML = <?php echo $iname; ?>sendMessageXHRO.responseXML;
        var oXMLData = oXML.getElementsByTagName("<?php echo $iname; ?>status");
        var sStatus = oXMLData[0].childNodes[0].data;
        if (sStatus=="received") {
            oMessageStatusUI.innerHTML = "Message sent";
        } else {
            oMessageStatusUI.innerHTML = "Message NOT sent";
        }
        oMessageSendButtonUI.disabled = false;
        oMessageResetButtonUI.disabled = false;
        oMessageSendButtonUI.value = "Send";
        oSendStatusTimer = window.setTimeout("<?php echo $iname; ?>clearSendStatus()", <?php echo $this->getConfig('UI_STATUS_SHOW_TIME'); ?>);
        <?php echo $iname; ?>loadMessages();
    }
}

function <?php echo $iname; ?>loadMessages() {
    var dDate = new Date();
    var tzoffset = dDate.getTimezoneOffset() / 60;
    <?php echo $iname; ?>loadMessagesXHRO = getXMLHTTPRequestObject();
    <?php echo $iname; ?>loadMessagesXHRO.onreadystatechange = <?php echo $iname; ?>loadMessagesXHROHandler;
    <?php echo $iname; ?>loadMessagesXHRO.open("GET", "<?php echo $this->getInput('xml_message_route'); ?>?hc=xmlmessages&tzoffset=" + tzoffset + "&lmts=" + <?php echo $iname; ?>sLastMessageHash, true);
    <?php echo $iname; ?>loadMessagesXHRO.send(" ");
}

function <?php echo $iname; ?>clearSendStatus() {
    var oMessageStatusUI = document.getElementById("<?php echo $iname; ?>sendstatus");
    oMessageStatusUI.innerHTML = "";
}

function <?php echo $iname; ?>sendMessage() {
    var oMessageTextUI = document.getElementById("<?php echo $iname; ?>sendmessage");
    var oMessageNameUI = document.getElementById("<?php echo $iname; ?>sendname");
    var oMessageSendButtonUI = document.getElementById("<?php echo $iname; ?>sendsubmit");
    var oMessageResetButtonUI = document.getElementById("<?php echo $iname; ?>sendreset");

    <?php echo $iname; ?>validateSendMessage();
    if (oMessageNameUI.value=="") {
        oMessageNameUI.focus();
        window.alert("You must enter your name.");
        return false;
    } else if (oMessageTextUI.value=="") {
        oMessageTextUI.focus();
        window.alert("You must type a message.");
        return false;
    } else {
        var dDate = new Date();
        var tzoffset = dDate.getTimezoneOffset() / 60;
        var sPostData = "hc=xmlsendmessage&tzoffset=" + tzoffset  + "&sendname=" + encodeURIComponent(oMessageNameUI.value) + "&sendmessage=" + encodeURIComponent(oMessageTextUI.value);
        window.clearTimeout(oSendStatusTimer);
        <?php echo $iname; ?>sendMessageXHRO = getXMLHTTPRequestObject();
        <?php echo $iname; ?>sendMessageXHRO.onreadystatechange = <?php echo $iname; ?>sendMessageXHROHandler;
        <?php echo $iname; ?>sendMessageXHRO.open("POST", "<?php echo $this->getInput('xml_send_message_route'); ?>", true);
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Content-length", sPostData.length);
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Connection", "close");
        <?php echo $iname; ?>sendMessageXHRO.send(sPostData);
        oMessageTextUI.value = "";
        oMessageSendButtonUI.value = "Sending message...";
        oSendStatusTimer = window.setTimeout("<?php echo $iname; ?>clearSendStatus()", <?php echo $this->getConfig('UI_STATUS_SHOW_TIME'); ?>);
        oMessageTextUI.focus();
        sOrigName = oMessageNameUI.value;
        <?php echo $iname; ?>validateSendMessage();
        return false;
    }
}

<?php echo $iname; ?>loadMessagesXHRO = getXMLHTTPRequestObject();
if (<?php echo $iname; ?>loadMessagesXHRO) {
    <?php echo $iname; ?>oLoadMessagesTimer = window.setInterval("<?php echo $iname; ?>loadMessages()", <?php echo $this->getConfig('MESSAGES_REFRESH_DELAY'); ?>);
}

// -->
</script>
<div class="darkchat">

<?php

// Display messages

echo "<div id=\"" . $iname . "messages\" class=\"messages\">\r\n\r\n";
if($this->getInput('messages')) foreach ($this->getInput('messages') as $message) {
    echo "<div class=\"messageblock\">\r\n";
    echo "  <div class=\"header\">\r\n";
    echo "    <div class=\"date_time\">" . $message["date_time"] . "</div>\r\n";
    echo "    <div class=\"username\"><b>" . $message["name"] . "</b> <i>said...</i></div>\r\n";
    echo "  </div>\r\n";
    echo "  <div class=\"messagetext\">\r\n";
    echo       $message["message"] . "\r\n";
    echo "  </div>\r\n";
    echo "</div>\r\n\r\n";
}
echo "</div>\r\n";

// Send Message UI

?>

<form name="<?php echo $iname; ?>frmsendmessage" method="post" action="<?php echo $this->getInput('route'); ?>" onsubmit="return <?php echo $iname; ?>sendMessage()">
<input type="hidden" name="hc" value="send">
<div class="sendmessage">
<table>
    <tr>
        <th align="right" valign="top"><label for="<?php echo $iname; ?>sendname">Name</label></th>
        <td align="left" valign="top"><input type="text" id="<?php echo $iname; ?>sendname" name="sendname" value="<?php echo $this->getInput('name'); ?>" size="32" maxlength="255"></td>
    </tr>
    <tr>
        <th align="right" valign="top"><label for="<?php echo $iname; ?>sendmessage">Message</label></th>
        <td align="left" valign="top"><textarea id="<?php echo $iname; ?>sendmessage" name="sendmessage" cols="40" rows="4" maxlength="4096"></textarea></td>
    </tr>
    <tr>
        <th align="right" valign="top"></th>
        <td align="left" valign="top">
            <input id="<?php echo $iname; ?>sendsubmit" type="submit" value="Send"> <input id="<?php echo $iname; ?>sendreset" type="reset" value="Clear"> 
            <span id="<?php echo $iname; ?>sendstatus"><?php echo $this->getInput('status'); ?></span>
        </td>
    </tr>
</table>
</div>
</form>

</div>

<script language="javascript" type="text/javascript">
<!--

var sOrigName = "<?php echo $this->getInput('name'); ?>";

function <?php echo $iname; ?>validateSendMessage() {
    var frm = document.forms["<?php echo $iname; ?>frmsendmessage"];
    var valid = false;
    frm.sendname.value.trim();
    frm.sendmessage.value.trim();
    if ((frm.sendname.value!="")&&(frm.sendmessage.value!="")) valid = true;
    if (frm.sendname.value>255) valid = false;
    if (frm.sendmessage.value>4096) valid = false;
    document.getElementById("<?php echo $iname; ?>sendsubmit").disabled = (!valid) ? true : false;
    document.getElementById("<?php echo $iname; ?>sendreset").disabled = ((frm.sendmessage.value=="")&&(frm.sendname.value==sOrigName)) ? true : false;
    return valid;
}

function <?php echo $iname; ?>handleNameKey(e) {
    var eEvent = e ? e : window.event;
    <?php echo $iname; ?>validateSendMessage();
}

function <?php echo $iname; ?>handleMessageKey(e) {
    var eEvent = e ? e : window.event;
    if ((eEvent.keyCode==13)&&(!eEvent.shiftKey)) {
        if (e) {
            eEvent.preventDefault();
        } else {
            eEvent.returnValue = false;
        }
        <?php echo $iname; ?>sendMessage();
    } else {
        <?php echo $iname; ?>validateSendMessage();
    }
}

document.getElementById("<?php echo $iname; ?>sendname").onfocus = <?php echo $iname; ?>handleNameKey;
document.getElementById("<?php echo $iname; ?>sendname").onblur = <?php echo $iname; ?>handleNameKey;
document.getElementById("<?php echo $iname; ?>sendname").onchange = <?php echo $iname; ?>handleNameKey;
document.getElementById("<?php echo $iname; ?>sendname").onkeypress = <?php echo $iname; ?>handleNameKey;
document.getElementById("<?php echo $iname; ?>sendmessage").onfocus = <?php echo $iname; ?>handleMessageKey;
document.getElementById("<?php echo $iname; ?>sendmessage").onblur = <?php echo $iname; ?>handleMessageKey;
document.getElementById("<?php echo $iname; ?>sendmessage").onchange = <?php echo $iname; ?>handleMessageKey;
document.getElementById("<?php echo $iname; ?>sendmessage").onkeypress = <?php echo $iname; ?>handleMessageKey;
<?php echo $iname; ?>validateSendMessage();

// -->
</script>

