<?

class ElearningSessionUtils extends ElearningSessionDB {

  var $currentClosedStatus;

  var $elearningSubscriptionUtils;
  var $elearningSessionCourseUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->currentClosedStatus = "elearningCurrentClosedStatus";
  }

  function deleteSession($elearningSessionId) {
    // Check that there are no subscriptions to the session
    if (!$elearningSubscriptions = $this->elearningSubscriptionUtils->selectBySessionId($elearningSessionId)) {
      // Delete the session course links
      if (!$elearningSessionCourses = $this->elearningSessionCourseUtils->selectBySessionId($elearningSessionId)) {
        $this->delete($elearningSessionId);
      }
    }
  }

  function sessionHasCourses($elearningSessionId) {
    if ($elearningSessionCourses = $this->elearningSessionCourseUtils->selectBySessionId($elearningSessionId)) {
      if (count($elearningSessionCourses) > 0) {
        return(true);
      }
    }

    return(false);
  }

}

?>
