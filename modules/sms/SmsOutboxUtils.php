<?

class SmsOutboxUtils extends SmsOutboxDB {

  var $websiteText;

  var $currentSmsStatus;

  function SmsOutboxUtils() {
    $this->SmsOutboxDB();

    $this->currentSmsStatus = "smsCurrentSmsStatus";
  }

}

?>
