<?

class ElearningAssignmentUtils extends ElearningAssignmentDB {

  var $websiteText;

  var $languageUtils;
  var $clockUtils;
  var $elearningResultUtils;
  var $elearningSubscriptionUtils;

  function ElearningAssignmentUtils() {
    $this->ElearningAssignmentDB();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function isClosed($elearningAssignment) {
    $closed = false;

    $systemDate = $this->clockUtils->getSystemDate();

    if ($elearningAssignment) {
      $closingDate = $elearningAssignment->getClosingDate();
      if ($this->clockUtils->systemDateIsSet($closingDate) && $this->clockUtils->systemDateIsGreater($systemDate, $closingDate)) {
        $closed = $closingDate;
      }
    }

    return($closed);
  }

  function setElearningResult($elearningSubscriptionId, $elearningExerciseId, $elearningResultId) {
    if ($elearningAssignment = $this->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
      $elearningAssignment->setElearningResultId($elearningResultId);
      $this->update($elearningAssignment);
    }
  }

  // Render the graph for the assignments results
  function renderGraph($elearningSubscriptionId) {
    global $gImagesUserUrl;

    $this->loadLanguageTexts();

    $str = '';

    if ($elearningSubscription = $this->elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
      $classId = $elearningSubscription->getClassId();
      $userId = $elearningSubscription->getUserId();

      if ($elearningClass = $this->elearningClassUtils->selectById($classId)) {
        $className = $elearningClass->getName();
      } else {
        $className = '';
      }

      if ($user = $this->userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        if ($firstname || $lastname) {
          $name = "$firstname $lastname";
        } else {
          $name = $email;
        }
      }

      $str = '';

      $str .= "\n<div class='elearning_exercise'>";

      $str .= "\n<div class='elearning_course_list_title'>" . $this->websiteText[0] . "</div>";

      $str .= "\n<br/>";

      $str .= "\n<div class='elearning_course_list_participant_name'>" . $this->websiteText[1] . ' ' . $name . "</div>";

      if ($className) {
        $str .= "\n<br/>";

        $str .= "\n<div class='elearning_course_list_class_name'>" . $this->websiteText[2] . ' ' . $className . "</div>";
      }

      $elearningResults = array();
      if ($elearningAssignments = $this->selectBySubscriptionId($elearningSubscriptionId)) {
        foreach ($elearningAssignments as $elearningAssignment) {
          $elearningResultId = $elearningAssignment->getElearningResultId();
          if ($elearningResult = $this->elearningResultUtils->selectById($elearningResultId)) {
            array_push($elearningResults, $elearningResult);
          }
        }
      }

      if (count($elearningResults) > 0) {
        $str .= "\n<br/>";

        $str .= $this->elearningResultUtils->renderResultsGraph($elearningResults);

        $str = "<table style='width:100%;'><tr><td>$str</td></tr></table>";
      }

      $str .= "\n</div>";
    }

    return($str);
  }


  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    $str = "\n<div class='elearning_assignment_list'>The list of assignments"
      . "<div class='elearning_assignment_list_title'>The title of the page</div>"
      . "<div class='elearning_assignment_list_comment'>A text</div>"
      . "</div>";

    return($str);
  }

}

?>
