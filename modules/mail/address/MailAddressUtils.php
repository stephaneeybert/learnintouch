<?

class MailAddressUtils extends MailAddressDB {

  var $websiteText;

  var $languageUtils;
  var $mailListAddressUtils;
  var $mailListUtils;

  function MailAddressUtils() {
    $this->MailAddressDB();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Subscribe to the address book
  function subscribe($email, $firstname = '', $lastname = '') {
    if ($email && LibEmail::validate($email) && LibEmail::validateDomain($email)) {
      if (!$mailAddress = $this->selectByEmail($email)) {
        $mailAddress = new MailAddress();
        $mailAddress->setEmail($email);
        $mailAddress->setFirstname($firstname);
        $mailAddress->setLastname($lastname);
        $mailAddress->setSubscribe(true);
        $this->insert($mailAddress);
        $mailAddressId = $this->getLastInsertId();

        return($mailAddressId);
      }
    }
  }

  function deleteAddress($mailAddressId) {
    if ($mailListAddresses = $this->mailListAddressUtils->selectByMailAddressId($mailAddressId)) {
      foreach ($mailListAddresses as $mailListAddress) {
        $mailListAddressId = $mailListAddress->getId();
        $this->mailListAddressUtils->delete($mailListAddressId);
      }
    }

    $this->delete($mailAddressId);
  }

  // Render the mini email address registration form
  function renderMiniRegister() {
    global $gMailUrl;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='mail_subscription'>";

    $str .= "\n<div class='mail_subscription_comment'>" . $this->websiteText[56] . "</div>";

    $str .= "\n<div class='mail_subscription_label'>" . $this->websiteText[57] . "</div>";

    $str .= "\n<form id='mailMiniRegistration' name='mailMiniRegistration' action='$gMailUrl/address/subscribe.php' method='post'>";

    $str .= "\n<div class='mail_subscription_field'>"
      . "<input class='mail_subscription_input' type='text' name='email' size='6' maxlength='255' />"
      . "</div>";

    $mailLists = $this->mailListUtils->selectAutoSubscribe();
    foreach ($mailLists as $mailList) {
      $mailListId = $mailList->getId();
      $name = $mailList->getName();
      $description = $mailList->getDescription();
      $str .= "\n<div class='mail_subscription_field' title='$description'><input type='checkbox' name='autoSubscribe_$mailListId' value='1' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">$name</span></div>";
    }

    $str .= "<div class='mail_subscription_okay_button'>"
      // An input field is required to have the browser submit the form on Enter key press
      // Otherwise a form with more than one input field is not submitted
      . "<input type='submit' value='' style='display:none;' />"
      . "<a href='#' onclick=\"document.forms['mailMiniRegistration'].submit(); return false;\">" . $this->websiteText[59] . "</a>"
      . "</div>";

    $str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

    $str .= "\n</form>";

    $str .= "\n</div>";

    return($str);
  }

}

?>
