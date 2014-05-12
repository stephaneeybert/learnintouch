<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $formValues = array();

  $formId = LibEnv::getEnvHttpPOST("formId");

  if ($form = $formUtils->selectById($formId)) {
    $formName = $form->getName();
    $description = $form->getDescription();
    $email = $form->getEmail();
    $currentLanguageCode = $languageUtils->getCurrentLanguageCode();
    $acknowledge = $languageUtils->getTextForLanguage($form->getAcknowledge(), $currentLanguageCode);
    $webpageId = $form->getWebpageId();

    $mailSubject = $form->getMailSubject();
    $mailMessage = $form->getMailMessage();
    $mailMessage = nl2br($mailMessage);

    $email = strtolower($email);

    $formItems = $formItemUtils->selectByFormId($formId);

    // Retrieve the firstname and lastname values, if any
    // These may be used if an email address value is to be saved in the mail address book
    $firstname = '';
    $lastname = '';
    foreach ($formItems as $formItem) {
      $itemType = $formItem->getType();
      if ($itemType == 'FORM_ITEM_FIRSTNAME') {
        $value = LibEnv::getEnvHttpPOST(FORM_ITEM_FIRSTNAME_NAME);
        $firstname = LibString::cleanString($value);
      } else if ($itemType == 'FORM_ITEM_LASTNAME') {
        $value = LibEnv::getEnvHttpPOST(FORM_ITEM_LASTNAME_NAME);
        $lastname = LibString::cleanString($value);
      }
    }

    foreach ($formItems as $formItem) {
      $formItemId = $formItem->getId();
      $itemType = $formItem->getType();
      $name = $formItem->getName();
      $text = $languageUtils->getTextForLanguage($formItem->getText(), $currentLanguageCode);
      $inMailAddress = $formItem->getInMailAddress();
      $mailListId = $formItem->getMailListId();

      $formItemWarnings = array();
      $warningPrefix = $websiteText[0] . " '" . strtolower($name) . "' ";

      $formValues[$name] = LibEnv::getEnvHttpPOST($name);
      $formValues[$name] = LibString::cleanString($formValues[$name]);
      $value = $formValues[$name];

      $formValids = $formValidUtils->selectByFormItemId($formItemId);
      foreach ($formValids as $formValid) {
        $validType = $formValid->getType();
        $boundary = $formValid->getBoundary();
        $message = $languageUtils->getTextForLanguage($formValid->getMessage(), $currentLanguageCode);

        $warning = str_replace('???', $boundary, $message);

        if ($validType == 'FORM_VALID_NOT_EMPTY') {
          if (!$value) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[1];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_MAXLENGTH') {
          if (strlen($value) > $boundary) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[2];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_MINLENGTH') {
          if (strlen($value) < $boundary) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[3];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_MAXVALUE') {
          if ($value > $boundary) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[4];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_MINVALUE') {
          if ($value < $boundary) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[5];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_EMAIL') {
          if (!LibEmail::validate($value)) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[6];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_BANKCARD') {
          if (!LibString::isBankCard($value)) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[7];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_IS_DATE') {
          if (!$clockUtils->isLocalNumericDateValid($value)) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[8];
            }
            array_push($formItemWarnings, $warning);
          }
        } else if ($validType == 'FORM_VALID_IS_NUMBER') {
          if (!is_numeric($value)) {
            if (!$message) {
              $warning = $warningPrefix . ' ' . $websiteText[9];
            }
            array_push($formItemWarnings, $warning);
          }
        }
      }

      if ($itemType == 'FORM_ITEM_EMAIL') {
        if ($mailListId) {
          $mailAddressId = $mailAddressUtils->subscribe($value, $firstname, $lastname);
          if ($mailAddressId) {
            $mailListAddressUtils->subscribe($mailListId, $mailAddressId) ;
          }
        } else if ($inMailAddress) {
          $mailAddressUtils->subscribe($value, $firstname, $lastname);
        }
      } else if ($itemType == 'FORM_ITEM_SECURE_CODE') {
        $randomSecurityCode = LibSession::getSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE);
        $securityCode = LibEnv::getEnvHttpPOST("securityCode");
        if (!$securityCode) {
          // The security code is required
          array_push($formItemWarnings, $websiteText[33]);
        } else if ($securityCode != $randomSecurityCode) {
          // The security code is incorrect
          array_push($formItemWarnings, $websiteText[34]);
        }
      }

      if (count($formItemWarnings) > 0) {
        $warnings[$formItemId] = $formItemWarnings;
      }
    }

    if (count($warnings) == 0) {

      $strFormVariables = '';
      foreach ($formItems as $formItem) {
        $name = $formItem->getName();
        $text = $languageUtils->getTextForLanguage($formItem->getText(), $currentLanguageCode);

        if ($name) {
          $value = $formValues[$name];
        } else {
          $value = "";
        }

        $strFormVariables .= "<br>$text : $value";
      }

      if ($websiteUtils->isCurrentWebsiteModule('MODULE_CONTACT')) {
        $subject = $websiteText[14] . ' ' . $formName;
        $message = $websiteText[12] . "<br />" . $strFormVariables;
        $contactUtils->registerMessage($email, $subject, $message, '', '', '', '', '');
      }

      if (LibEmail::validate($email)) {
        $websiteName = $profileUtils->getProfileValue("website.name");
        $websiteEmail = $profileUtils->getProfileValue("website.email");
        if (!$mailSubject) {
          $mailSubject = "$websiteText[10] $websiteName";
        }

        if (!$mailMessage) {
          $mailMessage = "$websiteText[11] $websiteName";
        }

        $mailBody = $mailMessage
          . "<br /><br />"
          . $websiteText[12]
          . "<br />"
          . $strFormVariables
          . "<br /><br />"
          . $websiteText[13]
          . "<br /><br />"
          . $websiteName;

        LibEmail::sendMail($email, $email, $mailSubject, $mailBody, $websiteEmail, $websiteName);
      }

      if ($webpageId) {
        $url = $templateUtils->renderPageUrl($webpageId);

        // The form hidden parameters are passed along as some may be required further down
        $urlParams = '';
        foreach ($formItems as $formItem) {
          $itemType = $formItem->getType();
          if ($itemType == 'FORM_ITEM_HIDDEN') {
            $name = $formItem->getName();
            $value = LibEnv::getEnvHttpPOST($name);
            $value = LibString::cleanString($value);
            // The specified url may already contain a ? and parameters
            if (!strstr($url, '?')) {
              $urlParams .= '?';
            } else {
              $urlParams .= '&';
            }
            $urlParams .= $name . '=' . $value;
          }
        }
        $urlParams = urlencode($urlParams);
        $url .= $urlParams;
      } else if ($acknowledge) {
        $url = "$gFormUrl/acknowledge.php?formId=$formId";
      } else {
        $url = $gHomeUrl;
      }

      $str = LibHtml::urlRedirect($url);
      printContent($str);
      exit;
    }
  }

}

?>
