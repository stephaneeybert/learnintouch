<?

class MailListUtils extends MailListDB {

  var $currentMailList;

  var $mailListUserUtils;
  var $mailHistoryUtils;
  var $mailListAddressUtils;

  function MailListUtils() {
    $this->MailListDB();
  }

  function init() {
    $this->currentMailList = "mailCurrentMailList";
  }

  // Delete a mailing list
  function deleteMailList($mailListId) {
    if ($mailHistories = $this->mailHistoryUtils->selectByMailListId($mailListId)) {
      foreach ($mailHistories as $mailHistory) {
        $mailHistory->setMailListId('');
        $this->mailHistoryUtils->update($mailHistory);
      }
    }

    $this->mailListAddressUtils->deleteByMailListId($mailListId);

    $this->mailListUserUtils->deleteByMailListId($mailListId);

    $this->delete($mailListId);
  }

}

?>
