<?PHP

class ElearningClassUtils extends ElearningClassDB {

  var $elearningSubscriptionUtils;

  function ElearningClassUtils() {
    $this->ElearningClassDB();
  }

  function deleteClass($elearningClassId) {
    if ($elearningSubscriptions = $this->elearningSubscriptionUtils->selectByClassId($elearningClassId)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        $elearningSubscription->setClassId('');
        $elearningSubscriptionUtils->update($elearningSubscription);
      }
    }

    $this->delete($elearningClassId);
  }

}

?>
