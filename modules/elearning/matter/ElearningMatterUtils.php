<?

class ElearningMatterUtils extends ElearningMatterDB {

  var $elearningCourseUtils;

  function __construct() {
    parent::__construct();
  }

  function deleteMatter($elearningMatterId) {
    // Check that there are no courses using the matter
    if (!$elearningCourses = $this->elearningCourseUtils->selectByMatterId($elearningMatterId)) {
      $this->delete($elearningMatterId);
    }
  }

}

?>
