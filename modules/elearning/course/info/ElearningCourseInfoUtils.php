<?

class ElearningCourseInfoUtils extends ElearningCourseInfoDB {

  var $websiteText;

  var $languageUtils;
  var $elearningCourseUtils;

  function ElearningCourseInfoUtils() {
    $this->ElearningCourseInfoDB();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the next available list order
  function getNextListOrder($elearningCourseId) {
    $listOrder = 1;
    if ($elearningCourseInfos = $this->selectByCourseId($elearningCourseId)) {
      $total = count($elearningCourseInfos);
      if ($total > 0) {
        $elearningCourseInfo = $elearningCourseInfos[$total - 1];
        $listOrder = $elearningCourseInfo->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Swap the curent object with the next one
  function swapWithNext($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the next object and its list order
    if (!$nextObject = $this->selectNext($id)) {
      return;
    }
    $nextListOrder = $nextObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($nextListOrder);
    $this->update($currentObject);
    $nextObject->setListOrder($currentListOrder);
    $this->update($nextObject);
  }

  // Swap the curent object with the previous one
  function swapWithPrevious($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the previous object and its list order
    if (!$previousObject = $this->selectPrevious($id)) {
      return;
    }
    $previousListOrder = $previousObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($previousListOrder);
    $this->update($currentObject);
    $previousObject->setListOrder($currentListOrder);
    $this->update($previousObject);
  }

  // Repair the order if some order numbers are identical
  // If, by accident, some objects have the same list order
  // (it shouldn't happen) then assign a new list order to each of them
  function repairListOrder($id) {
    if ($elearningCourseInfo = $this->selectById($id)) {
      $listOrder = $elearningCourseInfo->getListOrder();
      $elearningCourseId = $elearningCourseInfo->getElearningCourseId();
      if ($elearningCourseInfos = $this->selectByListOrder($elearningCourseId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningCourseInfos)) > 1) {
          $this->resetListOrder($elearningCourseId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($elearningCourseInfo = $this->selectById($id)) {
      $listOrder = $elearningCourseInfo->getListOrder();
      $elearningCourseId = $elearningCourseInfo->getElearningCourseId();
      if ($elearningCourseInfo = $this->selectByNextListOrder($elearningCourseId, $listOrder)) {
        return($elearningCourseInfo);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($elearningCourseInfo = $this->selectById($id)) {
      $listOrder = $elearningCourseInfo->getListOrder();
      $elearningCourseId = $elearningCourseInfo->getElearningCourseId();
      if ($elearningCourseInfo = $this->selectByPreviousListOrder($elearningCourseId, $listOrder)) {
        return($elearningCourseInfo);
      }
    }
  }

  // Place the current object before another target one
  function placeBefore($currentObjectId, $targetObjectId) {
    if ($currentObjectId == $targetObjectId) {
      return;
    }

    if ($nextObject = $this->selectNext($currentObjectId)) {
      if ($nextObject->getId() == $targetObjectId) {
        return;
      }
    }

    $currentObject = $this->selectById($currentObjectId);

    if ($targetObject = $this->selectById($targetObjectId)) {
      $targetObjectListOrder = $targetObject->getListOrder();
    } else {
      $targetObjectListOrder = '';
    }

    // Reset the list order of the target object and all its followers
    $elearningCourseId = $targetObject->getElearningCourseId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByCourseId($elearningCourseId)) {
      $nextListOrder = $targetObjectListOrder + 1;
      foreach($objects as $object) {
        $listOrder = $object->getListOrder();
        // Do not reset the list order of the objects preceding the target object
        if ($listOrder < $targetObjectListOrder) {
          continue;
        }
        $object->setListOrder($nextListOrder);
        $this->update($object);
        $nextListOrder++;
      }
    }

    // Update the list order of the current object
    // and set it with the list order of the specified target
    $currentObject->setListOrder($targetObjectListOrder);
    $currentObject->setElearningCourseId($targetObject->getElearningCourseId());
    $this->update($currentObject);

    return(true);
  }

  // Place the current object after another target one
  function placeAfter($currentObjectId, $targetObjectId) {
    if ($currentObjectId == $targetObjectId) {
      return;
    }

    if ($nextObject = $this->selectPrevious($currentObjectId)) {
      if ($nextObject->getId() == $targetObjectId) {
        return;
      }
    }

    $currentObject = $this->selectById($currentObjectId);

    if ($targetObject = $this->selectById($targetObjectId)) {
      $targetObjectListOrder = $targetObject->getListOrder();
    } else {
      $targetObjectListOrder = '';
    }

    // Reset the list order of the followers of the target object
    $elearningCourseId = $targetObject->getElearningCourseId();
    $currentListOrder = $currentObject->getListOrder();

    if ($objects = $this->selectByCourseId($elearningCourseId)) {
      $nextListOrder = $targetObjectListOrder + 2;
      foreach($objects as $object) {
        $listOrder = $object->getListOrder();
        // Do not reset the list order of the objects preceding or equal to the target object
        if ($listOrder <= $targetObjectListOrder) {
          continue;
        }
        $object->setListOrder($nextListOrder);
        $this->update($object);
        $nextListOrder++;
      }
    }

    // Update the list order of the current object
    // and set it with the list order of the specified target
    $currentObject->setListOrder($targetObjectListOrder + 1);
    $currentObject->setElearningCourseId($targetObject->getElearningCourseId());
    $this->update($currentObject);

    return(true);
  }

  // Check if the course information was created by the user
  function createdByUser($elearningCourseInfoId, $userId) {
    if ($elearningCourseInfo = $this->selectById($elearningCourseInfoId)) {
      $elearningCourseId = $elearningCourseInfo->getElearningCourseId();
      if ($this->elearningCourseUtils->createdByUser($elearningCourseId, $userId)) {
        return(true);
      }
    }

    return(false);
  }

  // Delete an exercise page from an exercise
  function deleteCourseInfo($elearningCourseInfoId) {
    $this->delete($elearningCourseInfoId);
  }

  // Get the next course information
  function getNextCourseInfo($elearningCourseInfoId) {
    if ($elearningCourseInfo = $this->selectNext($elearningCourseInfoId)) {
      $elearningCourseInfoId = $elearningCourseInfo->getId();
    }

    return($elearningCourseInfoId);
  }

  // Get the previous course information
  function getPreviousCourseInfo($elearningCourseInfoId) {
    if ($elearningCourseInfo = $this->selectPrevious($elearningCourseInfoId)) {
      $elearningCourseInfoId = $elearningCourseInfo->getId();
    }

    return($elearningCourseInfoId);
  }

  function render($elearningCourseId) {
    global $gImagesUserUrl;

    $str = '';

    if ($elearningCourseInfos = $this->selectByCourseId($elearningCourseId)) {
      $this->loadLanguageTexts();

      $courseInfoTextId = "course_info_text_$elearningCourseId";
      $courseInfoButtonId = "course_info_button_$elearningCourseId";

      $textButtonShow = $this->websiteText[0];
      $textButtonHide = $this->websiteText[1];

      $str = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function(){
  $('#$courseInfoButtonId').click(function(event) {
    toggleElementDisplay('$courseInfoButtonId', '$courseInfoTextId', '$textButtonShow', '$textButtonHide');
    return false;
  });
});
</script>
HEREDOC;

      $str .= "<img src='$gImagesUserUrl/" . IMAGE_ELEARNING_INFO . "' class='no_style_image_icon' title='$textButtonShow' style='border-width:0px; vertical-align:middle; margin-right:4px;' /> <a href='#' id='$courseInfoButtonId' onclick='return false;'> $textButtonShow</a>";

      $str .= "\n<div id='$courseInfoTextId' style='display:none;'>";

      foreach ($elearningCourseInfos as $elearningCourseInfo) {
        $headline = $elearningCourseInfo->getHeadline();
        $information = $elearningCourseInfo->getInformation();

        $str .= "\n<div class='elearning_course_info'>"
          . "<div class='elearning_course_info_headline'>"
          . $headline
          . "</div>"
          . "<div class='elearning_course_info_text'>"
          . $information
          . "</div>"
          . "</div>";
      }

      $str .= "</div>";
    }

    return($str);
  }

}

?>
