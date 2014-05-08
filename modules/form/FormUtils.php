<?

class FormUtils extends FormDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $popupUtils;
  var $templateUtils;
  var $formItemUtils;
  var $formValidUtils;
  var $fileUploadUtils;

  function FormUtils() {
    $this->FormDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'form/image/';
    $this->imageFileUrl = $gDataUrl . '/form/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'form')) {
        mkdir($gDataPath . 'form');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "FORM_IMAGE_WIDTH" =>
      array($this->mlText[40], $this->mlText[41], PREFERENCE_TYPE_TEXT, 300),
        "FORM_PHONE_IMAGE_WIDTH" =>
        array($this->mlText[42], $this->mlText[43], PREFERENCE_TYPE_TEXT, 140),
        );

    $this->preferenceUtils->init($this->preferences);
  }

  // Duplicate a form
  function duplicate($formId, $name) {
    if ($form = $this->selectById($formId)) {
      $form->setName($name);
      $this->insert($form);
      $duplicatedFormId = $this->getLastInsertId();
      if ($duplicatedFormId) {
        if ($formItems = $this->formItemUtils->selectByFormId($formId)) {
          foreach ($formItems as $formItem) {
            $formItemId = $formItem->getId();
            $formItem->setFormId($duplicatedFormId);

            $this->formItemUtils->insert($formItem);
            $duplicatedFormItemId = $this->formItemUtils->getLastInsertId();
            if ($duplicatedFormItemId) {
              if ($formValids = $this->formValidUtils->selectByFormItemId($formItemId)) {
                foreach ($formValids as $formValid) {
                  $formValid->setFormItemId($duplicatedFormItemId);
                  $this->formValidUtils->insert($formValid);
                }
              }
            }
          }
        }
      }

      return($duplicatedFormId);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imageFilePath . $oneFile)) {
            @unlink($this->imageFilePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Render the image
  function renderImage($image) {
    global $gDataPath;
    global $gDataUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $str = '';

    $imagePath = $this->imageFilePath;
    $imageUrl = $this->imageFileUrl;

    // Resize the image to the following width
    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("FORM_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("FORM_IMAGE_WIDTH");
    }

    if ($image && @file_exists($imagePath . $image)) {
      $str .= "<div class='form_image'>";
      if (LibImage::isImage($imagePath . $image)) {
        if ($width && !$this->fileUploadUtils->isGifImage($imagePath . $image)) {
          // The image is created on the fly
          $filename = urlencode($imagePath . $image);
          $url = $gUtilsUrl . "/printImage.php?filename=" . $filename
            . "&amp;width=" . $width . "&amp;height=";
        } else {
          $url = $imageUrl . '/' . $image;
        }
        $str .= "<img class='form_image_file' src='$url' title='' alt='' />";
      } else {
        $libFlash = new LibFlash();
        if ($libFlash->isFlashFile($imageFile)) {
          $str .= $libFlash->renderObject("$imageUrl/$image");
        }
      }
      $str .= "</div>";
    }

    return($str);
  }

  // Render a form
  function render($formId, $warnings = '') {
    global $gFormUrl;
    global $gUtilsUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='form'>";

    if ($form = $this->selectById($formId)) {
      $description = $form->getDescription();
      $image = $form->getImage();
      $email = $form->getEmail();
      $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();
      $title = $this->languageUtils->getTextForLanguage($form->getTitle(), $currentLanguageCode);
      $instructions = $this->languageUtils->getTextForLanguage($form->getInstructions(), $currentLanguageCode);

      $securityCodeFontSize = $this->templateUtils->getSecurityCodeFontSize($gIsPhoneClient);

      $str .= "\n<div class='form_title'>$title</div>";

      $str .= "\n<div class='form_description'>$description</div>";

      $strImage = $this->renderImage($image);

      $str .= "\n<div class='form_image'>$strImage</div>";

      $str .= "\n<div class='form_instructions'>$instructions</div>";

      $str .= "\n<form action='$gFormUrl/display.php' method='post'>";

      $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();

      $formItems = $this->formItemUtils->selectByFormId($formId);

      foreach ($formItems as $formItem) {
        $formItemId = $formItem->getId();
        $type = $formItem->getType();
        $name = $formItem->getName();
        $text = $this->languageUtils->getTextForLanguage($formItem->getText(), $currentLanguageCode);

        $size = $formItem->getSize();
        $maxlength = $formItem->getMaxlength();
        $help = $formItem->getHelp();
        $defaultValue = $formItem->getDefaultValue();

        // An item value can be specified when calling the form
        $value = LibEnv::getEnvHttpGET("$name");
        if (!$value) {
          // Retrieve the item value if the form validation failed
          $value = LibEnv::getEnvHttpPOST("$name");
        }

        $value = LibString::cleanString($value);

        if ($type == 'FORM_ITEM_INPUT_FIELD' || $type == 'FORM_ITEM_EMAIL' || $type == 'FORM_ITEM_FIRSTNAME' || $type == 'FORM_ITEM_LASTNAME') {
          if (!$size) {
            $size = 25;
          }
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          if ($type == 'FORM_ITEM_FIRSTNAME') {
            $name = FORM_ITEM_FIRSTNAME_NAME;
          } else if ($type == 'FORM_ITEM_LASTNAME') {
            $name = FORM_ITEM_LASTNAME_NAME;
          }
          $strField = "<input class='form_item_input' type='text' name='$name' value='$value' size='$size' maxlength='$maxlength' />";
        } else if ($type == 'FORM_ITEM_COMMENT') {
          $strField = '';
        } else if ($type == 'FORM_ITEM_SECURE_CODE') {
          $randomSecurityCode = LibUtils::generateUniqueId();
          LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, $randomSecurityCode);
          $url = $gUtilsUrl . "/printNumberImage.php?securityCodeFontSize=$securityCodeFontSize";
          $text = $this->mlText[23];
          $help = $this->mlText[24];
          $strField = "<input class='form_item_input' type='text' name='securityCode' size='5' maxlength='5' value='' /> <img src='$url' title='". $this->mlText[22] . "' alt='' />";
        } else if ($type == 'FORM_ITEM_INPUT_PASSWORD') {
          if (!$size) {
            $size = 25;
          }
          $strField = "<input class='form_item_input' type='password' name='$name' size='$size' maxlength='$maxlength' />";
        } else if ($type == 'FORM_ITEM_INPUT_TEXT') {
          $cols = 23;
          $rows = 5;
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          $strField = "<textarea class='form_item_input' name='$name' cols='$cols' rows='$rows'>$value</textarea>";
        } else if ($type == 'FORM_ITEM_DROP_DOWN') {
          $valueList = $this->formItemUtils->getValueList($formItemId);
          $valueList = LibUtils::arrayMerge(array('0' => ''), $valueList);
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          $strField = LibHtml::getSelectList($name, $valueList, $value);
        } else if ($type == 'FORM_ITEM_LIST') {
          $valueList = $this->formItemUtils->getValueList($formItemId);
          $valueList = LibUtils::arrayMerge(array('0' => ''), $valueList);
          if (!$size) {
            $size = 5;
          }
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          $strField = LibHtml::getSelectList($name, $valueList, $value, false, $size);
        } else if ($type == 'FORM_ITEM_RADIO') {
          $valueList = $this->formItemUtils->getValueList($formItemId);
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          $strField = '';
          foreach ($valueList as $listKey => $listValue) {
            if ($value && $value == $listKey) {
              $checked = "checked";
            } else {
              $checked = '';
            }
            $strField .= " (<input class='form_item_input' style='border:none 0px; vertical-align: middle;' type='radio' name='$name' id='$name' $checked value='$listKey'> $listValue)";
          }
        } else if ($type == 'FORM_ITEM_CHECKBOX') {
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          if ($value) {
            $checked = "checked='checked'";
          } else {
            $checked = '';
          }
          $strField = "<input class='form_item_input' type='checkbox' name='$name' $checked value='1' />";
        } else if ($type == 'FORM_ITEM_HIDDEN') {
          if (!$value && $defaultValue) {
            $value = $defaultValue;
          }
          $strField = "<input type='hidden' name='$name' value='$value' />";
        } else if ($type == 'FORM_ITEM_SUBMIT') {
          if ($text) {
            $button = $text;
          } else if ($defaultValue) {
            $button = $defaultValue;
          } else if ($name) {
            $button = $name;
          } else {
            $button = 'Submit';
          }
          $strField = "<input class='form_item_input' type='submit' value='$button' />";
          $text = '';
        }

        if ($help) {
          $label = $this->popupUtils->getUserTipPopup($text, $help, 300, 200);
        } else {
          $label = $text;
        }

        if (isset($warnings[$formItemId])) {
          $formItemWarnings = $warnings[$formItemId];
          if (count($formItemWarnings) > 0) {
            $str .= "\n<div>" . $this->commonUtils->renderWarningMessages($formItemWarnings) . "</div>";
          }
        }

        $str .= " <span class='form_item_label'>$label</span>"
          . "<span class='form_item_field'>$strField</span>";
      }

      $str .= "<input type='hidden' name='formSubmitted' value='1' />";
      $str .= "<input type='hidden' name='formId' value='$formId' />";

      $str .= "\n</form>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Get the list of all the forms
  function getFormNames() {
    $this->loadLanguageTexts();

    $listForms = array();

    if ($forms = $this->selectAll()) {
      foreach ($forms as $form) {
        $id = $form->getId();
        $name = $form->getName();

        $listForms[$id] = $this->websiteText[0] . ' ' . $name;
      }
    }

    return($listForms);
  }

  // Get the list of forms
  function getListUrls() {
    $this->loadLanguageTexts();

    $list = array();

    if ($forms = $this->selectAll()) {
      foreach ($forms as $form) {
        $formId = $form->getId();
        $name = $form->getName();

        $list['SYSTEM_PAGE_FORM' . $formId] = $this->mlText[0] . ' ' . $name;
      }
    }

    return($list);
  }

  // Delete a form
  function deleteForm($formId) {
    // Delete the form items
    if ($formItems = $this->formItemUtils->selectByFormId($formId)) {
      foreach ($formItems as $formItem) {
        $formItemId = $formItem->getId();
        $this->formItemUtils->deleteFormItem($formItemId);
      }
    }

    $this->delete($formId);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;

    $str = "<div class='form'>A form"
      . "<div class='form_title'>The form title</div>"
      . "<div class='form_description'>The form description</div>"
      . "<div class='form_image'>The image of the form"
      . "<img class='form_image_file' src='$gStylingImage' title='The border of the image of the form' alt='' />"
      . "</div>"
      . "<div class='form_instructions'>The instructions of the form</div>"
      . "<div><span class='form_item_label'>A field label</span>"
      . " <span class='form_item_field'>"
      . "<input class='form_item_input' type='text' name='word' value='An input field' size='30' maxlength='50'>"
      . "</span></div>"
      . "</div>";

    return($str);
  }

}

?>
