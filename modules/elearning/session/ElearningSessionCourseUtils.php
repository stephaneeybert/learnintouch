<?

class ElearningSessionCourseUtils extends ElearningSessionCourseDB {

  function ElearningSessionCourseUtils() {
    $this->ElearningSessionCourseDB();
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
