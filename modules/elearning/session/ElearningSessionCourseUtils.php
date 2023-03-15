<?

class ElearningSessionCourseUtils extends ElearningSessionCourseDB {

  function __construct() {
    parent::__construct();
  }

  function deleteSessionCourse($elearningSessionId, $elearningCourseId) {
    if ($elearningSessionCourse = $this->selectBySessionIdAndCourseId($elearningSessionId, $elearningCourseId))
    {
      $elearningSessionCourseId = $elearningSessionCourse->getId();
      $this->delete($elearningSessionCourseId);
    }
  }

}

?>
