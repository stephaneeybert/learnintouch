<?

class ElearningCourseItemUtils extends ElearningCourseItemDB {

  function ElearningCourseItemUtils() {
    $this->ElearningCourseItemDB();
  }

  // Get the next available list order
  function getNextListOrder($courseId) {
    $listOrder = 1;
    if ($objects = $this->selectByCourseId($courseId)) {
      $total = count($objects);
      if ($total > 0) {
        $object = $objects[$total - 1];
        $listOrder = $object->getListOrder() + 1;
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
    if ($elearningCourseItem = $this->selectById($id)) {
      $listOrder = $elearningCourseItem->getListOrder();
      $elearningCourseId = $elearningCourseItem->getElearningCourseId();
      if ($elearningCourseItems = $this->selectByListOrder($elearningCourseId, $listOrder)) {
        if (($listOrder == 0) || (count($elearningCourseItems)) > 1) {
          $this->resetListOrder($elearningCourseId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($object = $this->selectById($id)) {
      $listOrder = $object->getListOrder();
      $elearningCourseId = $object->getElearningCourseId();
      if ($object = $this->selectByNextListOrder($elearningCourseId, $listOrder)) {
        return($object);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($object = $this->selectById($id)) {
      $listOrder = $object->getListOrder();
      $elearningCourseId = $object->getElearningCourseId();
      if ($object = $this->selectByPreviousListOrder($elearningCourseId, $listOrder)) {
        return($object);
      }
    }
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
    if ($currentObject->getElearningCourseId() == $targetObject->getElearningCourseId()) {
      $this->update($currentObject);
    } else {
      if (($currentObject->getElearningExerciseId() && !$this->selectByCourseIdAndExerciseId($elearningCourseId, $currentObject->getElearningExerciseId())) || ($currentObject->getElearningLessonId() && !$this->selectByCourseIdAndLessonId($elearningCourseId, $currentObject->getElearningLessonId()))) {
        $currentObject->setElearningCourseId($targetObject->getElearningCourseId());
        $this->update($currentObject);
      }
    }

    return(true);
  }

}

?>
