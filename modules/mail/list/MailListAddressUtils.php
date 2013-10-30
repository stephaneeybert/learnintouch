<?

class MailListAddressUtils extends MailListAddressDB {

  function MailListAddressUtils() {
    $this->MailListAddressDB();
  }

  // Subscribe to a mailing list
  function subscribe($mailListId, $mailAddressId) {
    if (!$mailListAddress = $this->selectByMailListIdAndMailAddressId($mailListId, $mailAddressId)) {
      $mailListAddress = new MailListAddress();
      $mailListAddress->setMailAddressId($mailAddressId);
      $mailListAddress->setMailListId($mailListId);
      $this->insert($mailListAddress);
      $mailListAddressId = $this->getLastInsertId();

      return($mailListAddressId);
    }
  }

}

?>
