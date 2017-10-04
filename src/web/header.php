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
    if (<?php echo $iname; ?>sendMessageXHRO.readyState == 4) {
        var oMessageSendButtonUI = document.getElementById("<?php echo $iname; ?>sendsubmit");
        var oMessageResetButtonUI = document.getElementById("<?php echo $iname; ?>sendreset");
        var oXML = <?php echo $iname; ?>sendMessageXHRO.responseXML;
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
        <?php echo $iname; ?>oSendStatusTimer = window.setTimeout("<?php echo $iname; ?>clearSendStatus()", <?php echo $this->getConfig('UI_STATUS_SHOW_TIME'); ?>);
        <?php echo $iname; ?>loadMessages();
    }
}

function <?php echo $iname; ?>loadMessages() {
    var dDate = new Date();
    var tzoffset = 0 - (dDate.getTimezoneOffset() / 60);
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
        var tzoffset = 0 - (dDate.getTimezoneOffset() / 60);
        var sPostData = "hc=xmlsendmessage&tzoffset=" + tzoffset  + "&sendname=" + encodeURIComponent(oMessageNameUI.value) + "&sendmessage=" + encodeURIComponent(oMessageTextUI.value);
        window.clearTimeout(<?php echo $iname; ?>oSendStatusTimer);
        <?php echo $iname; ?>sendMessageXHRO = getXMLHTTPRequestObject();
        <?php echo $iname; ?>sendMessageXHRO.onreadystatechange = <?php echo $iname; ?>sendMessageXHROHandler;
        <?php echo $iname; ?>sendMessageXHRO.open("POST", "<?php echo $this->getInput('xml_send_message_route'); ?>", true);
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Content-length", sPostData.length);
        <?php echo $iname; ?>sendMessageXHRO.setRequestHeader("Connection", "close");
        <?php echo $iname; ?>sendMessageXHRO.send(sPostData);
        oMessageTextUI.value = "";
        oMessageSendButtonUI.value = "Sending message...";
        <?php echo $iname; ?>oSendStatusTimer = window.setTimeout("<?php echo $iname; ?>clearSendStatus()", <?php echo $this->getConfig('UI_STATUS_SHOW_TIME'); ?>);
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
<div class="darkchat" id="darkchat<?php echo $iname; ?>">