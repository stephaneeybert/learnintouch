<?

class SmsNumberUtils extends SmsNumberDB {

  var $websiteText;

  var $languageUtils;
  var $preferenceUtils;
  var $smsListNumberUtils;
  var $smsListUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Subscribe a mobile phone number
  function subscribe($mobilePhone) {
    if ($mobilePhone) {
      if (!$smsNumber = $this->selectByMobilePhone($mobilePhone)) {
        $smsNumber = new SmsNumber();
        $smsNumber->setMobilePhone($mobilePhone);
        $smsNumber->setSubscribe(true);
        $this->insert($smsNumber);

        return(true);
      }
    }

    return(false);
  }

  function deleteSmsNumber($smsNumberId) {
    if ($smsListNumbers = $this->smsListNumberUtils->selectBySmsNumberId($smsNumberId)) {
      foreach ($smsListNumbers as $smsListNumber) {
        $smsListNumberId = $smsListNumber->getId();
        $this->smsListNumberUtils->delete($smsListNumberId);
      }
    }

    $this->delete($smsNumberId);
  }

  // Render the mini mobile phone number registration form
  function renderMiniNumberRegister() {
    global $gSmsUrl;
    global $gImagesUserUrl;

    $str = '';

    $str .= "\n<div class='sms_subscription'>";

    $str .= "\n<form id='smsMiniRegistration' name='smsMiniRegistration' action='$gSmsUrl/number/subscribe.php' method='post'>";

    $title = $this->preferenceUtils->getValue("SMS_REGISTER_TITLE");

    if (!$title) {
      $title = $this->websiteText[60];
    }

    $description = $this->preferenceUtils->getValue("SMS_REGISTER_DESCRIPTION");

    if (!$description) {
      $description = $this->websiteText[61];
    }

    $str .= "\n<div class='sms_subscription_title'>";
    $str .= $title;
    $str .= "\n</div>";

    $str .= "\n<div class='sms_subscription_comment'>";
    $str .= $description;
    $str .= "\n</div>";

    $str .= "\n<div class='sms_subscription_label'>" . $this->websiteText[63] . "</div>";

    $str .= "\n<div class='sms_subscription_field'>"
      . "<input class='sms_subscription_input' type='text' name='mobilePhone' size='12' maxlength='20' />"
      . "</div>";

    $smsLists = $this->smsListUtils->selectAutoSubscribe();
    foreach ($smsLists as $smsList) {
      $smsListId = $smsList->getId();
      $name = $smsList->getName();
      $description = $smsList->getDescription();
      $str .= "\n<div class='sms_subscription_field' title='$description'><input type='checkbox' name='autoSubscribe_$smsListId' value='1' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">$name</span></div>";
    }

    $str .= "<div class='sms_subscription_okay_button'>"
      // An input field is required to have the browser submit the form on Enter key press
      // Otherwise a form with more than one input field is not submitted
      . "<input type='submit' value='' style='display:none;' />"
      . "<a href='#' onclick=\"document.forms['smsMiniRegistration'].submit(); return false;\">" . $this->websiteText[59] . "</a>"
      . "</div>";

    $str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

    $str .= "\n</form>";

    $str .= "\n</div>";

    return($str);
  }

}

?>
