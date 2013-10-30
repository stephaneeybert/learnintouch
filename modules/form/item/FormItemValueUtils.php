<?

class FormItemValueUtils extends FormItemValueDB {

  var $formUtils;
  var $formItemUtils;

  function FormItemValueUtils() {
    $this->FormItemValueDB();
  }

  // Add a value to a form item
  function add($formItemId) {
    $formItemValue = new FormItemValue();
    $formItemValue->setFormItemId($formItemId);
    $this->insert($formItemValue);
  }

  // Get the first value of a form item
  // Otherwise get the first value of the first item of the first form
  function getFirstFormValue($formItemId = '') {
    $formItemValueId = '';

    if (!$formItemId) {
      if ($forms = $this->formUtils->selectAll()) {
        if (count($forms) > 0) {
          $form = $forms[0];
          $formId = $form->getId();

          if ($formItems = $this->formItemUtils->selectByFormId($formId)) {
            if (count($formItems) > 0) {
              $formItem = $formItems[0];
              $formItemId = $formItem->getId();
            }
          }
        }
      }
    }

    if ($formItemValues = $this->selectByFormItemId($formItemId)) {
      if (count($formItemValues) > 0) {
        $formItemValue = $formItemValues[0];
        $formItemValueId = $formItemValue->getId();
      }
    }

    return($formItemValueId);
  }

  // Get the previous value
  function getPreviousFormValueId($formItemValueId) {
    $previousFormValueId = '';

    if ($formItemValue = $this->selectById($formItemValueId)) {
      $formItemId = $formItemValue->getFormItemId();

      if ($formItemValues = $this->selectByFormItemId($formItemId)) {
        foreach ($formItemValues as $formItemValue) {
          $wFormItemValueId = $formItemValue->getId();
          if ($wFormItemValueId == $formItemValueId) {
            return($previousFormValueId);
          }
          $previousFormValueId = $wFormItemValueId;
        }
      }
    }
  }

  // Get the next value
  function getNextFormValueId($formItemValueId) {
    $previousFormValueId = '';

    if ($formItemValue = $this->selectById($formItemValueId)) {
      $formItemId = $formItemValue->getFormItemId();

      if ($formItemValues = $this->selectByFormItemId($formItemId)) {
        foreach ($formItemValues as $formItemValue) {
          $wFormItemValueId = $formItemValue->getId();
          if ($previousFormValueId == $formItemValueId) {
            return($wFormItemValueId);
          }
          $previousFormValueId = $wFormItemValueId;
        }
      }
    }
  }

  // Delete a form item value
  function deleteFormItemValue($formItemValueId) {
    $this->delete($formItemValueId);
  }

}

?>
