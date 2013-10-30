<?

class ContactStatusUtils extends ContactStatusDB {

  var $contactUtils;

  function ContactStatusUtils() {
    $this->ContactStatusDB();
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($statuses = $this->selectAll()) {
      $total = count($statuses);
      if ($total > 0) {
        $status = $statuses[$total - 1];
        $listOrder = $status->getListOrder() + 1;
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
    if ($contactStatus = $this->selectById($id)) {
      $listOrder = $contactStatus->getListOrder();
      if ($contactStatuses = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($contactStatuses)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($status = $this->selectById($id)) {
      $listOrder = $status->getListOrder();
      if ($status = $this->selectByNextListOrder($listOrder)) {
        return($status);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($status = $this->selectById($id)) {
      $listOrder = $status->getListOrder();
      if ($status = $this->selectByPreviousListOrder($listOrder)) {
        return($status);
      }
    }
  }

  // Delete a status
  function deleteStatus($contactStatusId) {
    if ($contacts = $this->contactUtils->selectAllByStatusId($contactStatusId)) {
      foreach ($contacts as $contact) {
        $contact->setStatus('');
        $this->contactUtils->update($contact);
      }
    }

    $this->delete($contactStatusId);
  }

}

?>
