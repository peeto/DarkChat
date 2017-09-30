<?php

if (include("sitechat_header.php")) return ("");

if (!$sitechatembedded) {

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/tr/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<title>Site chat</title>

<meta name="robots" content="noindex, nofollow" />

<style type="text/css">
<!--

body {
	margin: 0px;
}

.sitechat {
	display: block;
	margin: 0px;
	padding: 0px;
}

.sitechat form {
	display: inline;
	margin: 0px;
	padding: 0px;
}

.sitechat .messages {
	display: block;
	overflow: scroll;
	height: 300px;
	margin: 0px;
}

.sitechat .messages .messageblock {
	display: block;
	padding: 0.2em;
	margin: 0.4em 0.2em 0.6em 0.2em;
	background-color: #F8F8F8;
}

.sitechat .messages .messageblock .header {
	display: block;
	padding: 0.2em;
	margin: 0.2em;
	background-color: #E0E0E0;
}

.sitechat .messages .messageblock .messagetext {
	display: block;
	padding: 0.2em;
	margin: 0.2em;
	background-color: #F8F8F8;
}

.sitechat .sendmessage {
	display: block;
	clear: none;
	padding: 0.2em;
	margin: 0px;
	background-color: #F8F8F8;
}

.sitechat .sendmessage table {
	margin: 0px;
	padding: 0px;
}

.sitechat .sendmessage table td {
	width: 100%;
}

#sendmessage, #sendname {
	width: 100%;
}

-->
</style>
<?php
}
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

function sitechatGetDOW( nDOW ) {
	// returns a day of the week as a string
	var a = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	return a[nDOW];
}

function sitechatGetMonthName( nMonth ) {
	// returns the name of a month as a string
	var a = new Array("January", "Febuary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	return a[nMonth];
}

function sitechatGetLastNDigits( n, d ) {
	// returns the last d digits of integer number n
	return Math.round(((n/Math.pow(10,d))-Math.floor(n/Math.pow(10,d)))*Math.pow(10,d));
}

function sitechatGetNumSuffix( n ) {
	// returns the suffix for an inter number ie: "th", "st", "nd", or "rd".
	var ld = sitechatGetLastNDigits( n, 1 );
	var l2d = sitechatGetLastNDigits( n, 2 );
	if ((l2d>=11)&&(l2d<=13)) {
		return "th";
	} else if (ld==1) {
		return "st";
	} else if (ld==2) {
		return "nd";
	} else if (ld==3) {
		return "rd";
	} else {
		return "th";
	}
}

function sitechatDateToString( sDate ) {
	var d = new Date( sDate );
	var sND = "";

	if (d.getHours()<10) {
		sND += "0" + String(d.getHours());
	} else if ((d.getHours()>12)&&(d.getHours()<22)) {
		sND += "0" + String(d.getHours()-12);
	} else if ((d.getHours()>=10)&&(d.getHours()<=12)) {
		sND += String(d.getHours());
	} else {
		sND += String(d.getHours()-12);
	}
	sND += ":" + ((d.getMinutes()<10) ? "0" : "") + String(d.getMinutes());
	sND += ":" + ((d.getSeconds()<10) ? "0" : "") + String(d.getSeconds());
	sND += " " + ((d.getHours()<12) ? "AM" : "PM");
	sND += " " + sitechatGetDOW(d.getDay()) + " " + d.getDate() + sitechatGetNumSuffix( d.getDate() );
	sND += " of " + sitechatGetMonthName(d.getMonth()) + " " + d.getFullYear();
	return sND;
}


var sitechatLoadMessagesXHRO;
var sitechatSendMessageXHRO;
var oLoadMessagesTimer = "";
var oSendStatusTimer = "";
var sLastMessageHash = "";

function sitechatLoadMessagesXHROHandler() {
	if (sitechatLoadMessagesXHRO.readyState == 4) {
		var oMessageStatusUI = document.getElementById("sendstatus");
	        if (sitechatLoadMessagesXHRO.status == 200) {
			var oMessageUI = document.getElementById("messages");
			var oXML = sitechatLoadMessagesXHRO.responseXML;
			var oXMLHeader = oXML.getElementsByTagName("messages");
			if (oXMLHeader[0]!=undefined) {
				sLastMessageHash = oXMLHeader[0].getAttribute("lmhash");
				var oXMLData = oXML.getElementsByTagName("message");
				var sHTML = "";
				for (var i = 0; i < oXMLData.length; i++) {
					sHTML += "<div class=\"messageblock\">\r\n";
					sHTML += "<div class=\"header\">\r\n";
					sHTML += "<div class=\"datetime\">" + sitechatDateToString(oXMLData[i].getElementsByTagName("datetime")[0].childNodes[0].data) + "</div>\r\n";
					sHTML += "<div class=\"username\"><b>" + oXMLData[i].getElementsByTagName("sendername")[0].childNodes[0].data + "</b> <i>said...</i></div>\r\n";
					sHTML += "</div>\r\n";
					sHTML += "<div class=\"messagetext\">" + oXMLData[i].getElementsByTagName("messagetext")[0].childNodes[0].data + "</div>\r\n";
					sHTML += "</div>\r\n\r\n";
				}
				if (sHTML!="") {
					oMessageUI.innerHTML = sHTML;
				} else {
					oMessageStatusUI.innerHTML = "Failed to retrieve new messages.";
				}
			}
	        } else {
			oMessageStatusUI.innerHTML = "Failed to retrieve new messages.";
		}
	}
}

function sitechatSendMessageXHROHandler() {
	if (sitechatSendMessageXHRO.readyState == 4) {
		var oMessageStatusUI = document.getElementById("sendstatus");
		var oMessageSendButtonUI = document.getElementById("sendsubmit");
		var oMessageResetButtonUI = document.getElementById("sendreset");
		var oXML = sitechatSendMessageXHRO.responseXML;
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
		oSendStatusTimer = window.setTimeout("sitechatClearSendStatus()", <?php echo SITECHATUISTATUSSHOWTIME; ?>);
		sitechatLoadMessages();
	}
}

function sitechatLoadMessages() {
	sitechatLoadMessagesXHRO = getXMLHTTPRequestObject();
	sitechatLoadMessagesXHRO.onreadystatechange = sitechatLoadMessagesXHROHandler;
	sitechatLoadMessagesXHRO.open("GET", "<?php echo $_SERVER["PHP_SELF"]; ?>?hc=xmlmessages&lmts=" + sLastMessageHash, true);
	sitechatLoadMessagesXHRO.send(" ");
}

function sitechatClearSendStatus() {
	var oMessageStatusUI = document.getElementById("sendstatus");
	oMessageStatusUI.innerHTML = "";
}

function sitechatSendMessage() {
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
		sitechatSendMessageXHRO = getXMLHTTPRequestObject();
		sitechatSendMessageXHRO.onreadystatechange = sitechatSendMessageXHROHandler;
		sitechatSendMessageXHRO.open("POST", "<?php echo $_SERVER["PHP_SELF"]; ?>", true);
		sitechatSendMessageXHRO.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		sitechatSendMessageXHRO.setRequestHeader("Content-length", sPostData.length);
		sitechatSendMessageXHRO.setRequestHeader("Connection", "close");
		sitechatSendMessageXHRO.send(sPostData);
		oMessageTextUI.value = "";
		oMessageSendButtonUI.value = "Sending message...";
		oSendStatusTimer = window.setTimeout("sitechatClearSendStatus()", <?php echo SITECHATUISTATUSSHOWTIME; ?>);
		oMessageTextUI.focus();
		sOrigName = oMessageNameUI.value;
		validateSendMessage();
		return false;
	}
}

sitechatLoadMessagesXHRO = getXMLHTTPRequestObject();
if (sitechatLoadMessagesXHRO) {
	oLoadMessagesTimer = window.setInterval("sitechatLoadMessages()", <?php echo SITECHATUIMESSAGESREFRESHDELAY; ?>);
}

// -->
</script>
<?php
if (!$sitechatembedded) {
?>
</head>
<body>
<?php
}
?>
<div class="sitechat">

<?php

// Display messages

echo "<div id=\"messages\" class=\"messages\">\r\n\r\n";
foreach ($messages as $message) {
	echo "<div class=\"messageblock\">\r\n";
	echo "<div class=\"header\">\r\n";
	echo "<div class=\"datetime\">" . date("h:i:s A l dS \of F Y", strtotime($message["datetime"])) . "</div>\r\n";
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

<form name="frmsendmessage" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" onsubmit="return sitechatSendMessage()">
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
  <td align="left" valign="top"><input id="sendsubmit" type="submit" value="Send"> <input id="sendreset" type="reset" value="Clear"> <span id="sendstatus"><?php echo $messagestatus; ?></span></td>
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
		sitechatSendMessage();
	} else {
		validateSendMessage();
	}
}

//document.getElementById("sendname").addEventListener("focus", handleNameKey, false);
//document.getElementById("sendname").addEventListener("blur", handleNameKey, false);
//document.getElementById("sendname").addEventListener("change", handleNameKey, false);
//document.getElementById("sendname").addEventListener("keypress", handleNameKey, false);

//document.getElementById("sendmessage").addEventListener("focus", handleMessageKey, false);
//document.getElementById("sendmessage").addEventListener("blur", handleMessageKey, false);
//document.getElementById("sendmessage").addEventListener("change", handleMessageKey, false);
//document.getElementById("sendmessage").addEventListener("keypress", handleMessageKey, false);

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

<p>Sitechat can be downloaded from <a href="http://peeto.net/host/sitechat.zip">http://peeto.net/host/sitechat.zip</a>.</p>

<?php
if (!$sitechatembedded) {
?>
</body>
</html>
<?php
}
?>
