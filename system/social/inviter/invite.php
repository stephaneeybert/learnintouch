<?php

require_once("website.php");
require_once($gApiPath . 'OpenInviter/openinviter.php');

$mlText = $languageUtils->getWebsiteText(__FILE__);

$maxContacts = 10;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $loginEmail = LibEnv::getEnvHttpPOST("loginEmail");
  $selectedProvider = LibEnv::getEnvHttpPOST("selectedProvider");
  $messageSubject = LibEnv::getEnvHttpPOST("messageSubject");
  $messageBody = LibEnv::getEnvHttpPOST("messageBody");

  $loginEmail = LibString::cleanString($loginEmail);
  $selectedProvider = LibString::cleanString($selectedProvider);
  $messageSubject = LibString::cleanString($messageSubject);
  $messageBody = LibString::cleanString($messageBody);

  $selectedContacts = array();
  $nbContacts = 0;
  foreach ($_POST as $key => $val) {
    if ($nbContacts < $maxContacts && is_numeric($val)) {
      eval("\$value = 'check_' . $val;");
      if ($_POST[$value] > 0) {
        $selectedContacts[$_POST['email_' . $val]] = $_POST['name_' . $val];
        $nbContacts++;
      }
    }
  }

  // A contact is required
  if (count($selectedContacts) == 0) {
    array_push($warnings, $mlText[15]);
  }

  if (count($warnings) == 0) {
    $websiteName = $profileUtils->getProfileValue("website.name");
    $websiteEmail = $profileUtils->getProfileValue("website.email");

    $strList = '';
    foreach ($selectedContacts as $email => $name) {
        $strList .= "<div>$email $name</div>";
      LibEmail::sendMail($email, $name, $messageSubject, $messageBody, $websiteEmail, $websiteName);
    }

    $str = "\n<div class='system'>"
      . "<div class='system_comment'>$mlText[16]</div>"
      . $strList
      . "</div>";
    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
  }

} else {

  $selectedProvider = '';
  $loginEmail = '';
  $messageSubject = '';
  $messageBody = $profileUtils->getInviteMessage() . ' '  . $gHomeUrl;

}

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<div class='system_comment'>$mlText[11]</div>";

$str .= "\n<div id='errorMessage' class='system_warning' ></div>";

$str .= "\n<form name='invite_form' id='invite_form' action='$gInviterUrl/invite.php' method='post'>";

$openInviter = new OpenInviter();
$openInviterServices = $openInviter->getPlugins();
$list = "<select name='selectedProvider'><option value=''></option>";
$providerSubset = array('yahoo', 'gmail', 'hotmail');
foreach ($openInviterServices as $openInviterType => $providers) {
  foreach ($providers as $providerId => $providerDetails) {
    if (in_array($providerId, $providerSubset)) {
      $list .= "<option value='{$providerId}'" . ($selectedProvider == $providerId ? ' selected' : '') . ">{$providerDetails['name']}</option>";
    }
  }
}
$list .= "</select>";

$str .= "\n<div class='system_label'>$mlText[8]</div>";
$str .= "\n<div class='system_field'>$list</div>";

$str .= "\n<div class='system_label'>$mlText[3]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' id='loginEmail' name='loginEmail' value='$loginEmail' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$mlText[4]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='password' id='password' name='password' value='' size='25' maxlength='20' onBlur='javascript:getContacts();' /></div>";

$str .= "\n<div class='system_label'>$mlText[18]</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='messageSubject' value='$messageSubject' size='25' maxlength='255' /></div>";

$str .= "\n<div class='system_label'>$mlText[17]</div>";
$str .= "\n<div class='system_field'><textarea class='system_input' name='messageBody' cols='30' rows='5'>$messageBody</textarea></div>";

$str .= "\n<div class='system_field'>"
  . "<div id='boutonGetContacts'><a href='javascript:getContacts();'>$mlText[5]</a></div>"
  . "<div id='buttonInvite' style='display:none;'><a href='#' onclick=\"document.forms['invite_form'].submit(); return false;\">$mlText[14]</a></div>"
  . "</div>";

$str .= "\n<div id='contactList' style='display:none;'>"
  . "<div class='system_comment'>$mlText[19] $maxContacts $mlText[20] $mlText[2]</div>"
  . "<pre id='contactListItems' style='overflow-y:auto; overflow-x:hidden; white-space:normal; max-height:200px;'></pre>"
  . "</div>";

$str .= <<<HEREDOC
<script type='text/javascript'>

  $(document).ready(function() {
    $("#loginEmail").keypress(function(event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });
    $("#password").keypress(function(event) {
      if (event.keyCode == 13) {
        if ($("#password").val() != '') {
          getContacts();
        }
        event.preventDefault();
        return false;
      }
    });
  });

  // Toggle all checkboxes
  function toggleAllCheckboxes() {
    $('input:checkbox').each(function() {
      this.checked = !this.checked;
    });
  }

  // Get the contacts
  function getContacts() {
    var loginEmail = document.invite_form.loginEmail.value;
    var password = document.invite_form.password.value;
    var selectedProvider = document.invite_form.selectedProvider.value;
    loginEmail = encodeURIComponent(loginEmail);
    password = encodeURIComponent(password);
    selectedProvider = encodeURIComponent(selectedProvider);
    var url = '$gInviterUrl/get_contacts.php?email='+loginEmail+'&password='+password+'&selectedProvider='+selectedProvider;
    ajaxAsynchronousRequest(url, renderContactList);
  }

  // Render a list of contacts with checkboxes
  function renderContactList(responseText) {
    var response = eval('(' + responseText + ')');
    var error = response.error;
    var contacts = response.contacts;

    // Prevent the typing of words if the number of typed in words exceeds the specified limit
    if (contacts.length > 0) {
      var table = document.createElement("TABLE");
      i = 0;
      for (var i in contacts) {
        var email = contacts[i].email;
        var name = contacts[i].name;
          var tr = document.createElement("TR");
          var td0 = document.createElement("TD");
          var checkboxInput = document.createElement("input");
          checkboxInput.type = "checkbox";
          checkboxInput.name = "check_" + i;
          checkboxInput.value = i;
          td0.appendChild(checkboxInput);
          var hiddenEmail = document.createElement("input");
          hiddenEmail.type = "hidden";
          hiddenEmail.name = "email_" + i;
          hiddenEmail.value = email;
          td0.appendChild(hiddenEmail);
          var hiddenName = document.createElement("input");
          hiddenName.type = "hidden";
          hiddenName.name = "name_" + i;
          hiddenName.value = name;
          td0.appendChild(hiddenName);
          var span = document.createElement("SPAN");
          span.title = email;
          if (name) {
            span.appendChild(document.createTextNode(name));
          } else {
            span.appendChild(document.createTextNode(email));
          }
          td0.appendChild(span);
          tr.appendChild(td0);
          table.appendChild(tr);

        i++;
      }
      document.getElementById("contactListItems").appendChild(table);
      document.getElementById("errorMessage").innerHTML = '';
      document.getElementById('buttonInvite').style.display = 'block';
      document.getElementById('contactList').style.display = 'block';
      document.getElementById('boutonGetContacts').style.display = 'none';
    } else {
      document.getElementById("errorMessage").innerHTML = error;
    }
  }

</script>
HEREDOC;

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
