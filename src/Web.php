<?php
namespace peeto/DarkChat;

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

var loadMessagesXHRO;
var sendMessageXHRO;
var oLoadMessagesTimer = "";
var oSendStatusTimer = "";
var sLastMessageHash = "";

function loadMessagesXHROHandler() {
    var oMessageStatusUI = document.getElementById("sendstatus");
    if (loadMessagesXHRO.readyState == 3) {
        oMessageStatusUI.innerHTML = "Retrieving new messages...";
    }
    if (loadMessagesXHRO.readyState == 4) {
        if (loadMessagesXHRO.status == 200) {
            var oMessageUI = document.getElementById("messages");
            var oXML = loadMessagesXHRO.responseXML;
            var oXMLHeader = oXML.getElementsByTagName("messages");
            if (oXMLHeader[0]!=undefined) {
                sLastMessageHash = oXMLHeader[0].getAttribute("lmhash");
                var oXMLData = oXML.getElementsByTagName("message");
                var sHTML = "";
                for (var i = 0; i < oXMLData.length; i++) {
                    sHTML += "<div class=\"messageblock\">\r\n";
                    sHTML += "  <div class=\"header\">\r\n";
                    sHTML += "      <div class=\"datetime\">" + oXMLData[i].getElementsByTagName("datetime")[0].childNodes[0].data + "</div>\r\n";
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

function sendMessageXHROHandler() {
    var oMessageStatusUI = document.getElementById("sendstatus");
    if (sendMessageXHRO.readyState == 3) {
        oMessageStatusUI.innerHTML = "Sending message...";
    }
    if (sendMessageXHRO.readyState == 4) {
        var oMessageSendButtonUI = document.getElementById("sendsubmit");
        var oMessageResetButtonUI = document.getElementById("sendreset");
        var oXML = sendMessageXHRO.responseXML;
        var oXMLData = oXML.getElementsByTagName("status");
        var sStatus = oXMLData[0].childNodes[0].data;
        if (sStatus=="received") {
            oMessageStatusUI.innerHTML = "Message sent";
        } else {
            oMessageStatusUI.innerHTML = "Message NOT sent";
        }
        oMessageSendButtonUI.disabled = false;
        oMessageResetButtonUI.disabled = false;
        oMessageSendButtonUI.value = "Send";
        oSendStatusTimer = window.setTimeout("clearSendStatus()", <?php echo SITECHATUISTATUSSHOWTIME; ?>);
        loadMessages();
    }
}

function loadMessages() {
    loadMessagesXHRO = getXMLHTTPRequestObject();
    loadMessagesXHRO.onreadystatechange = loadMessagesXHROHandler;
    loadMessagesXHRO.open("GET", "<?php echo $_SERVER["PHP_SELF"]; ?>?hc=xmlmessages&lmts=" + sLastMessageHash, true);
    loadMessagesXHRO.send(" ");
}

function clearSendStatus() {
    var oMessageStatusUI = document.getElementById("sendstatus");
    oMessageStatusUI.innerHTML = "";
}

function sendMessage() {
    var oMessageTextUI = document.getElementById("sendmessage");
    var oMessageNameUI = document.getElementById("sendname");
    var oMessageSendButtonUI = document.getElementById("sendsubmit");
    var oMessageResetButtonUI = document.getElementById("sendreset");

    validateSendMessage();
    if (oMessageNameUI.value=="") {
        oMessageNameUI.focus();
        window.alert("You must enter your name.");
        return false;
    } else if (oMessageTextUI.value=="") {
        oMessageTextUI.focus();
        window.alert("You must type a message.");
        return false;
    } else {
        var sPostData = "hc=xmlsendmessage&sendname=" + encodeURIComponent(oMessageNameUI.value) + "&sendmessage=" + encodeURIComponent(oMessageTextUI.value);
        window.clearTimeout(oSendStatusTimer);
        sendMessageXHRO = getXMLHTTPRequestObject();
        sendMessageXHRO.onreadystatechange = sendMessageXHROHandler;
        sendMessageXHRO.open("POST", "<?php echo $_SERVER["PHP_SELF"]; ?>", true);
        sendMessageXHRO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        sendMessageXHRO.setRequestHeader("Content-length", sPostData.length);
        sendMessageXHRO.setRequestHeader("Connection", "close");
        sendMessageXHRO.send(sPostData);
        oMessageTextUI.value = "";
        oMessageSendButtonUI.value = "Sending message...";
        oSendStatusTimer = window.setTimeout("clearSendStatus()", <?php echo SITECHATUISTATUSSHOWTIME; ?>);
        oMessageTextUI.focus();
        sOrigName = oMessageNameUI.value;
        validateSendMessage();
        return false;
    }
}

loadMessagesXHRO = getXMLHTTPRequestObject();
if (loadMessagesXHRO) {
    oLoadMessagesTimer = window.setInterval("loadMessages()", <?php echo SITECHATUIMESSAGESREFRESHDELAY; ?>);
}

// -->
</script>
<div class="darkchat">

<?php

// Display messages

echo "<div id=\"messages\" class=\"messages\">\r\n\r\n";
foreach ($messages as $message) {
    echo "<div class=\"messageblock\">\r\n";
    echo "<div class=\"header\">\r\n";
    echo "<div class=\"datetime\">" . $message["datetime"] . "</div>\r\n";
    echo "<div class=\"username\"><b>" . $message["name"] . "</b> <i>said...</i></div>\r\n";
    echo "</div>\r\n";
    echo "<div class=\"messagetext\">\r\n";
    echo $message["message"] . "\r\n";
    echo "</div>\r\n";
    echo "</div>\r\n\r\n";
}
echo "</div>\r\n";

// Send Message UI

?>

<form name="frmsendmessage" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" onsubmit="return sendMessage()">
<input type="hidden" name="hc" value="send">
<div class="sendmessage">
<table>
    <tr>
        <th align="right" valign="top"><label for="sendname">Name</label></th>
        <td align="left" valign="top"><input type="text" id="sendname" name="sendname" value="<?php echo $getname; ?>" size="32" maxlength="255"></td>
    </tr>
    <tr>
        <th align="right" valign="top"><label for="sendmessage">Message</label></th>
        <td align="left" valign="top"><textarea id="sendmessage" name="sendmessage" cols="40" rows="4" maxlength="4096"></textarea></td>
    </tr>
    <tr>
        <th align="right" valign="top"></th>
        <td align="left" valign="top">
            <input id="sendsubmit" type="submit" value="Send"> <input id="sendreset" type="reset" value="Clear"> 
            <span id="sendstatus"><?php echo $messagestatus; ?></span>
        </td>
    </tr>
</table>
</div>
</form>

</div>

<script language="javascript" type="text/javascript">
<!--

var sOrigName = "<?php echo $getname; ?>";

function validateSendMessage() {
    var frm = document.forms["frmsendmessage"];
    var valid = false;
    frm.sendname.value.trim();
    frm.sendmessage.value.trim();
    if ((frm.sendname.value!="")&&(frm.sendmessage.value!="")) valid = true;
    if (frm.sendname.value>255) valid = false;
    if (frm.sendmessage.value>4096) valid = false;
    document.getElementById("sendsubmit").disabled = (!valid) ? true : false;
    document.getElementById("sendreset").disabled = ((frm.sendmessage.value=="")&&(frm.sendname.value==sOrigName)) ? true : false;
    return valid;
}

function handleNameKey(e) {
    var eEvent = e ? e : window.event;
    validateSendMessage();
}

function handleMessageKey(e) {
    var eEvent = e ? e : window.event;
    if ((eEvent.keyCode==13)&&(!eEvent.shiftKey)) {
        if (e) {
            eEvent.preventDefault();
        } else {
            eEvent.returnValue = false;
        }
        sendMessage();
    } else {
        validateSendMessage();
    }
}

document.getElementById("sendname").onfocus = handleNameKey;
document.getElementById("sendname").onblur = handleNameKey;
document.getElementById("sendname").onchange = handleNameKey;
document.getElementById("sendname").onkeypress = handleNameKey;
document.getElementById("sendmessage").onfocus = handleMessageKey;
document.getElementById("sendmessage").onblur = handleMessageKey;
document.getElementById("sendmessage").onchange = handleMessageKey;
document.getElementById("sendmessage").onkeypress = handleMessageKey;
validateSendMessage();

// -->
</script>

