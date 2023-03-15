<?

class FormItemUtils extends FormItemDB {

  var $languageUtils;
  var $formValidUtils;
  var $formItemValueUtils;

  function __construct() {
    parent::__construct();
  }

  // Get the list of value text pairs
  function getValueList($formItemId) {
    $list = array();

    $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();

    $formItemValues = $this->formItemValueUtils->selectByFormItemId($formItemId);
    for ($i = 0; $i < count($formItemValues); $i++) {
      $formItemValue = $formItemValues[$i];
      $value = $formItemValue->getValue();
      $text = $this->languageUtils->getTextForLanguage($formItemValue->getText(), $currentLanguageCode);

      $list[$value] = $text;
    }

    return($list);
  }

  // Check if a form item has a type that uses a list of values
  function hasListType($formItemId) {
    $hasListType = false;

    if ($formItem = $this->selectById($formItemId)) {
      $type = $formItem->getType();

      $hasListType = $this->isListType($type);
    }

    return($hasListType);
  }

  // Check if a type uses a list of values
  function isListType($type) {
    $isListType = false;

    if ($type == 'FORM_ITEM_DROP_DOWN' || $type == 'FORM_ITEM_LIST' || $type == 'FORM_ITEM_RADIO') {
      $isListType = true;
    }

    return($isListType);
  }

  // Get the next available list order
  function getNextListOrder($formId) {
    $listOrder = 1;

    if ($formItems = $this->selectByFormId($formId)) {
      $total = count($formItems);
      if ($total > 0) {
        $formItem = $formItems[$total - 1];
        $listOrder = $formItem->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Get the next object
  function selectNext($id) {
    if ($formItem = $this->selectById($id)) {
      $listOrder = $formItem->getListOrder();
      $formId = $formItem->getFormId();
      if ($formItem = $this->selectByNextListOrder($formId, $listOrder)) {
        return($formItem);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($formItem = $this->selectById($id)) {
      $listOrder = $formItem->getListOrder();
      $formId = $formItem->getFormId();
      if ($formItem = $this->selectByPreviousListOrder($formId, $listOrder)) {
        return($formItem);
      }
    }
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
    if ($formItem = $this->selectById($id)) {
      $listOrder = $formItem->getListOrder();
      $formId = $formItem->getFormId();
      if ($formItems = $this->selectByListOrder($formId, $listOrder)) {
        if (($listOrder == 0) || (count($formItems)) > 1) {
          $this->resetListOrder($formId);
        }
      }
    }
  }

  // Delete a form item
  function deleteFormItem($formItemId) {
    // Delete the form item validators
    if ($formValids = $this->formValidUtils->selectByFormItemId($formItemId)) {
      foreach ($formValids as $formValid) {
        $formValidId = $formValid->getId();
        $this->formValidUtils->deleteFormValidator($formValidId);
      }
    }

    // Delete the form item values
    if ($formItemValues = $this->formItemValueUtils->selectByFormItemId($formItemId)) {
      foreach ($formItemValues as $formItemValue) {
        $formItemValueId = $formItemValue->getId();
        $this->formItemValueUtils->deleteFormItemValue($formItemValueId);
      }
    }

    $this->delete($formItemId);
  }

}

?>
