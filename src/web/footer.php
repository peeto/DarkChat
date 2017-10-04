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