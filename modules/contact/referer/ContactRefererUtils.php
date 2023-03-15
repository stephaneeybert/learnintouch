<?

class ContactRefererUtils extends ContactRefererDB {

  var $languageUtils;
  var $contactUtils;

  function __construct() {
    parent::__construct();
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($refereres = $this->selectAll()) {
      $total = count($refereres);
      if ($total > 0) {
        $referer = $refereres[$total - 1];
        $listOrder = $referer->getListOrder() + 1;
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
    if ($contactReferer = $this->selectById($id)) {
      $listOrder = $contactReferer->getListOrder();
      if ($contactRefereres = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($contactRefereres)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($referer = $this->selectById($id)) {
      $listOrder = $referer->getListOrder();
      if ($referer = $this->selectByNextListOrder($listOrder)) {
        return($referer);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($referer = $this->selectById($id)) {
      $listOrder = $referer->getListOrder();
      if ($referer = $this->selectByPreviousListOrder($listOrder)) {
        return($referer);
      }
    }
  }

  // Delete a referer
  function deleteReferer($contactRefererId) {
    if ($contacts = $this->contactUtils->selectAllByRefererId($contactRefererId)) {
      foreach ($contacts as $contact) {
        $contact->setContactRefererId('');
        $this->contactUtils->update($contact);
      }
    }

    $this->delete($contactRefererId);
  }

  // Get the list of referers
  function getList() {
    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    $list = array();
    if ($contactReferers = $this->selectAll()) {
      foreach ($contactReferers as $contactReferer) {
        $contactRefererId = $contactReferer->getId();
        $descriptions = $contactReferer->getDescription();
        $description = $this->languageUtils->getTextForLanguage($descriptions, $languageCode);

        $list[$contactRefererId] = $description;
      }
    }

    return($list);
  }

}

?>
