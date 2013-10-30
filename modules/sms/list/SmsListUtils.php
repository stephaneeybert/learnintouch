<?

class SmsListUtils extends SmsListDB {

  var $currentSmsList;

  var $smsListUserUtils;
  var $smsListNumberUtils;
  var $smsHistoryUtils;

  function SmsListUtils() {
    $this->SmsListDB();

    $this->init();
  }

  function init() {
    $this->currentSmsList = "smsCurrentSmsList";
  }

  // Delete an sms list
  function deleteSmsList($smsListId) {
    if ($smsHistories = $this->smsHistoryUtils->selectBySmsListId($smsListId)) {
      foreach ($smsHistories as $smsHistory) {
        $smsHistory->setSmsListId('');
        $this->smsHistoryUtils->update($smsHistory);
      }
    }

    $this->smsListNumberUtils->deleteBySmsListId($smsListId);

    $this->smsListUserUtils->deleteBySmsListId($smsListId);

    $this->delete($smsListId);
  }

}

?>
