<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  $email = LibEnv::getEnvHttpPOST("email");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $message = LibEnv::getEnvHttpPOST("message");
  $telephone = LibEnv::getEnvHttpPOST("telephone");
  $monday = LibEnv::getEnvHttpPOST("monday");
  $tuesday = LibEnv::getEnvHttpPOST("tuesday");
  $wednesday = LibEnv::getEnvHttpPOST("wednesday");
  $thursday = LibEnv::getEnvHttpPOST("thursday");
  $friday = LibEnv::getEnvHttpPOST("friday");
  $saturday = LibEnv::getEnvHttpPOST("saturday");
  $sunday = LibEnv::getEnvHttpPOST("sunday");
  $morning = LibEnv::getEnvHttpPOST("morning");
  $lunch = LibEnv::getEnvHttpPOST("lunch");
  $afternoon = LibEnv::getEnvHttpPOST("afternoon");
  $evening = LibEnv::getEnvHttpPOST("evening");

  if ($elearningExerciseUtils->contactPageInfoRequired()) {

    if (!$email) {
      array_push($warnings, $websiteText[0]);
    }

    if (!LibEmail::validate($email)) {
      array_push($warnings, $websiteText[28]);
    }

    if (!$firstname) {
      array_push($warnings, $websiteText[1]);
    }

    if (!$lastname) {
      array_push($warnings, $websiteText[2]);
    }

  }

  if (count($warnings) == 0) {

    if ($telephone) {
      $message .= '<br />' . $websiteText[10] . ' ' . $telephone;
    }

    if ($monday || $tuesday || $wednesday || $thursday || $friday || $saturday || $sunday) {
      $message .= '<br />' . $websiteText[24];
      if ($monday) {
        $message .= ' - ' . $websiteText[12];
      }
      if ($tuesday) {
        $message .= ' - ' . $websiteText[13];
      }
      if ($wednesday) {
        $message .= ' - ' . $websiteText[14];
      }
      if ($thursday) {
        $message .= ' - ' . $websiteText[15];
      }
      if ($friday) {
        $message .= ' - ' . $websiteText[16];
      }
      if ($saturday) {
        $message .= ' - ' . $websiteText[17];
      }
      if ($sunday) {
        $message .= ' - ' . $websiteText[18];
      }
    }
    if ($morning || $lunch || $afternoon || $evening) {
      $message .= '<br />' . $websiteText[25];
      if ($morning) {
        $message .= ' - ' . $websiteText[19];
      }
      if ($lunch) {
        $message .= ' - ' . $websiteText[20];
      }
      if ($afternoon) {
        $message .= ' - ' . $websiteText[21];
      }
      if ($evening) {
        $message .= ' - ' . $websiteText[22];
      }
    }

    $elearningResultId = $elearningExerciseUtils->saveExerciseResults($elearningExerciseId, $elearningSubscriptionId, $email, $firstname, $lastname, $message);

    if ($elearningResultId) {
      $elearningExerciseUtils->sendExerciseResults($elearningResultId, $elearningSubscriptionId, $elearningExerciseId, $email, $message);
    }

    $encodedEmail = urlencode($email);
    if ($elearningExerciseUtils->displayContactPageBeforeResults($elearningExerciseId)) {
      $redirectUrl = "$gElearningUrl/exercise/display_results.php?elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId&email=$encodedEmail";
    } else {
      $redirectUrl = "$gElearningUrl/exercise/display_contact_thanks.php?elearningExerciseId=$elearningExerciseId&email=$encodedEmail";
    }

    $templateUtils->storeRequestedUrl($redirectUrl);

    $str = LibHtml::urlRedirect($redirectUrl);
    printContent($str);
    exit;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

  $email = '';
  $firstname = '';
  $lastname = '';
  $telephone = '';
  $monday = '';
  $tuesday = '';
  $wednesday = '';
  $thursday = '';
  $friday = '';
  $saturday = '';
  $sunday = '';
  $morning = '';
  $lunch = '';
  $afternoon = '';
  $evening = '';

  $userId = $userUtils->getLoggedUserId();

  if ($userId) {
    if ($user = $userUtils->selectById($userId)) {
      $email = $user->getEmail();
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
    }
  } else {
    $email = LibCookie::getCookie($elearningExerciseUtils->cookieVisitorEmail);
  }

}

// Now the exercise answers may be reset, if configured so, since the exercise is completed
$elearningExerciseUtils->allowResetExercise($elearningExerciseId);

$withSocialConnect = false;
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  if ($elearningExercise->getSocialConnect()) {
    $withSocialConnect = true;
  }
}

$str = '';

$str .= "\n<div class='system'>";

$message = $elearningExerciseUtils->getHeaderMessageInContactPage();

$str .= "<div class='system_comment'>$message</div>";

$exerciseTimedOut = LibSession::getSessionValue(ELEARNING_SESSION_EXERCISE_TIME_OUT . $elearningExerciseId);
if ($exerciseTimedOut) {
  $timeOut = $elearningExerciseUtils->getTimeOutMessage();
  $str .= "<div class='system_warning'>" . $timeOut . "</div>";
}

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='contact_page_form' id='contact_page_form' action='$PHP_SELF' method='post'>";

if ($elearningExerciseUtils->contactPageInfoRequired()) {
  $str .= "<div class='system_comment'>$websiteText[26]</div>";
} else {
  $str .= "<div class='system_comment'>$websiteText[27]</div>";
}

if ($withSocialConnect) {
  $strPostLogin = <<<HEREDOC
<script language="javascript" type="text/javascript">
function postSocialLogin(response) {
  if (response.email) {
    document.contact_page_form.email.value = response.email;
    $("#emailLabel").attr("style", "display:none;");
    $("#email").attr("style", "display:none;");
  }
  if (response.firstname) {
    document.contact_page_form.firstname.value = response.firstname;
    $("#firstnameLabel").attr("style", "display:none;");
    $("#firstname").attr("style", "display:none;");
  }
  if (response.lastname) {
    document.contact_page_form.lastname.value = response.lastname;
    $("#lastnameLabel").attr("style", "display:none;");
    $("#lastname").attr("style", "display:none;");
  }
  $(".system_warning").attr("style", "display:none;");
}
</script>
HEREDOC;

  $str .= $strPostLogin
    . "<div class='system_comment'>";

  $strFacebookLogin = $facebookUtils->renderLoginSetup(true);
  if ($facebookUtils->isEnabled()) {
    $str .= $strFacebookLogin;
    $str .= ' ' . $facebookUtils->renderLoginButton();
  }

  $strLinkedinLogin = $linkedinUtils->renderLoginSetup(true);
  if ($linkedinUtils->isEnabled()) {
    $str .= $strLinkedinLogin;
    $str .= ' ' . $linkedinUtils->renderLoginButton();
  }

  $withGoogle = $googleUtils->setup(true);
  if ($googleUtils->isEnabled()) {
    $str .= ' ' . $googleUtils->renderLoginButton();
  }

  $str .= "</div>";
}

// Ask for the email so as to be able to store the exercise results
// or so as to send an email to a chosen teacher

$str .= "<div class='system_label' id='emailLabel'>" . $websiteText[4] . "</div>"
  . "<div class='system_field'><input class='system_input' type='text' id='email' name='email' value='$email' size='25' maxlength='255' /></div>";

if ($elearningExerciseUtils->contactPageInfoRequired()) {
  $str .= "<div class='system_label' id='firstnameLabel'>" . $websiteText[5] . "</div>"
    . "<div class='system_field'><input class='system_input' type='text' id='firstname' name='firstname' value='$firstname' size='25' maxlength='255' /></div>"
    . "<div class='system_label' id='lastnameLabel'>" . $websiteText[6] . "</div>"
    . "<div class='system_field'><input class='system_input' type='text' id='lastname' name='lastname' value='$lastname' size='25' maxlength='255' /></div>"
    . "<div class='system_label' id='telephoneLabel'>" . $websiteText[9] . "</div>"
    . "<div class='system_field'><input class='system_input' type='text' id='telephone' name='telephone' value='$telephone' size='25' maxlength='25' /></div>"
    . "<div class='system_label' id='daysLabel'>" . $websiteText[11] . "</div>"
    . "<div class='system_field'>"
    . " <span style='white-space:nowrap;'>$websiteText[12]<input type='checkbox' name='monday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[13]<input type='checkbox' name='tuesday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[14]<input type='checkbox' name='wednesday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[15]<input type='checkbox' name='thursday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[16]<input type='checkbox' name='friday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[17]<input type='checkbox' name='saturday' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[18]<input type='checkbox' name='sunday' value='1' /></span>"
    . "</div>"
    . "<div class='system_label' id='timesLabel'>"
    . $websiteText[23] . "</div>"
    . "<div class='system_field'>"
    . " <span style='white-space:nowrap;'>$websiteText[19]<input type='checkbox' name='morning' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[20]<input type='checkbox' name='lunch' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[21]<input type='checkbox' name='afternoon' value='1' /></span>"
    . " <span style='white-space:nowrap;'>$websiteText[22]<input type='checkbox' name='evening' value='1' /></span>"
    . "</div>";
}

$str .= "<div class='system_label'>"
  . $websiteText[7] . "</div>"
  . "<div class='system_field'>"
  . "<textarea rows='5' cols='23' class='system_input' name='message'></textarea>"
  . "</div>";

$str .= "\n<div class='system_okay_button'>"
  . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' class='no_style_image_icon' title='" . $websiteText[8] . "' style='vertical-align:middle;' /> "
  . " <a href='#' onclick=\"document.forms['contact_page_form'].submit(); return false;\" style='text-decoration:none;'>" . $websiteText[8] . "</a>"
  . "</div>";

$str .= "<input type='hidden' name='elearningExerciseId' value='$elearningExerciseId' />";
$str .= "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId' />";
$str .= "<input type='hidden' name='formSubmitted' value='1' />";

$str .= "</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
