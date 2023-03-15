<?

class SmsOutboxUtils extends SmsOutboxDB {

  var $websiteText;

  var $currentSmsStatus;

  function __construct() {
    parent::__construct();

    $this->currentSmsStatus = "smsCurrentSmsStatus";
  }

}

?>
