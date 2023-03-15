<?

class MailOutboxUtils extends MailOutboxDB {

  var $currentMailStatus;

  var $propertyUtils;
  var $userUtils;

  // The property name of a semaphore to check if a mailing is ongoing
  var $propertyMailingOngoing;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->currentMailStatus = "mailCurrentMailStatus";

    $this->propertyMailingOngoing = "MAIL_ONGOING";
  }

  function mailingOngoing() {
    $this->propertyUtils->store($this->propertyMailingOngoing, true);
  }

  function mailingEnded() {
    $this->propertyUtils->store($this->propertyMailingOngoing, false);
  }

  function cloneMailOutbox($mailOutbox) {
    $clone = new MailOutbox();
    $clone->setId($mailOutbox->getId());
    $clone->setFirstname($mailOutbox->getFirstname());
    $clone->setLastname($mailOutbox->getLastname());
    $clone->setEmail($mailOutbox->getEmail());
    $clone->setPassword($mailOutbox->getPassword());
    $clone->setSent($mailOutbox->getSent());
    $clone->setErrorMessage($mailOutbox->getErrorMessage());
    $clone->setMetaNames($mailOutbox->getMetaNames());

    return($clone);
  }

  function isMailingOnGoing() {
    $ongoing = $this->propertyUtils->retrieve($this->propertyMailingOngoing);

    return($ongoing);
  }

  // Convert a string to meta names
  function stringToMetaNames($strMetaNames) {
    $metaNames = array();

    $wMetaNames = explode(MAIL_META_NAME_SEPARATOR, $strMetaNames);
    foreach ($wMetaNames as $wMetaName) {
      if ($wMetaName) {
        list($name, $value) = explode(MAIL_META_NAME_VALUE_SEPARATOR, $wMetaName);
        $metaNames[$name] = $value;
      }
    }

    return($metaNames);
  }

  // Convert the meta names into a string
  function metaNamesToString($metaNames) {
    $strMetaNames = '';

    if (is_array($metaNames) && count($metaNames) > 0) {
      foreach ($metaNames as $metaNameKey => $metaNameValue) {
        if ($strMetaNames) {
          $strMetaNames .= MAIL_META_NAME_SEPARATOR;
        }
        $metaNameKey = str_replace(MAIL_META_NAME_VALUE_SEPARATOR, '', $metaNameKey);
        $metaNameValue = str_replace(MAIL_META_NAME_VALUE_SEPARATOR, '', $metaNameValue);
        $strMetaNames .= $metaNameKey . MAIL_META_NAME_VALUE_SEPARATOR . $metaNameValue;
      }
    }

    return($strMetaNames);
  }

  // Replace the meta names with values
  function parseMetaNames($body, $mailOutboxId) {
    if ($mailOutbox = $this->selectById($mailOutboxId)) {
      $firstname = $mailOutbox->getFirstname();
      $lastname = $mailOutbox->getLastname();
      $email = $mailOutbox->getEmail();
      $password = $mailOutbox->getPassword();

      $body = str_replace(MAIL_META_USER_FIRSTNAME, $firstname, $body);
      $body = str_replace(MAIL_META_USER_LASTNAME, $lastname, $body);
      $body = str_replace(MAIL_META_USER_EMAIL, $email, $body);
      $body = str_replace(MAIL_META_USER_PASSWORD, $password, $body);
    }

    return($body);
  }

  // Check if the mail body contains some meta names left
  function getRemainingMetaNames($body) {
    $metaNames = '';

    if (strstr($body, '[') && preg_match_all("(\[[_A-Z]+\])", $body, $matches)) {
      $matches = $matches[0];
      $temp = array_unique($matches);
      $matches = array_values($temp);
      foreach ($matches as $match) {
        $metaNames .= $match . ' ';
      }
    }

    return($metaNames);
  }

  // Replace the custom meta names if any
  function parseCustomMetaName($body, $metaNames) {
    foreach ($metaNames as $name => $value) {
      $body = str_replace('['.$name.']', $value, $body);
    }

    return($body);
  }

}

?>
