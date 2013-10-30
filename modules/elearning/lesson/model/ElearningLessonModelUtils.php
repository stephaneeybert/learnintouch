<?

class ElearningLessonModelUtils extends ElearningLessonModelDB {

  var $adminUtils;
  var $elearningLessonUtils;
  var $elearningLessonHeadingUtils;

  function ElearningLessonModelUtils() {
    $this->ElearningLessonModelDB();
  }

  function deleteModel($elearningLessonModelId) {
    if ($elearningLessons = $this->elearningLessonUtils->selectByLessonModelId($elearningLessonModelId)) {
      foreach ($elearningLessons as $elearningLesson) {
        $elearningLesson->setLessonModelId('');
        $this->elearningLessonUtils->update($elearningLesson);
      }
    }

    if ($elearningLessonHeadings = $this->elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
      foreach ($elearningLessonHeadings as $elearningLessonHeading) {
        $elearningLessonHeadingId = $elearningLessonHeading->getId();
        $this->elearningLessonHeadingUtils->deleteHeading($elearningLessonHeadingId);
      }
    }

    $this->delete($elearningLessonModelId);
  }

  // Check if the lesson model is locked for the logged in admin
  function isLockedForLoggedInAdmin($elearningLessonModelId) {
    $locked = false;

    $adminLogin = $this->adminUtils->checkAdminLogin();
    if (!$this->adminUtils->isSuperAdmin($adminLogin)) {
      if ($elearningLessonModel = $this->selectById($elearningLessonModelId)) {
        $locked = $elearningLessonModel->getLocked();
      }
    }

    return($locked);
  }

}

?>
