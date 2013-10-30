<?php

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

// Call the controller
$quickRegistration = false;
$mlText = $languageUtils->getMlText($gUserPath . "registerController.php");
require_once($gUserPath . "registerController.php");

$mlText = $languageUtils->getMlText(__FILE__);

// Init the unset variables
if (!$formSubmitted) {
  $firstname = '';
  $lastname = '';
  $email = '';
  $mobilePhone = '';
  $subscribe = 1;
  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

$panelUtils->setHeader($mlText[0], "$gUserUrl/admin.php");
$panelUtils->addLine('', $panelUtils->addCell($mlText[1], "nb"));
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[3], "br"), "<input type='text' name='firstname' size='30' maxlength='255' value='$firstname'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "br"), "<input type='text' name='lastname' size='30' maxlength='255' value='$lastname'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type='text' name='email' size='30' maxlength='255' value='$email'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[11], "br"), "<input type='text' name='mobilePhone' size='20' maxlength='20' value='$mobilePhone'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "br"), "<input type='password' name='password1' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<input type='password' name='password2' size='10' maxlength='10'>");

$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('securityCode', 1);
$panelUtils->addHiddenField('termsOfService', 1);
LibSession::putSessionValue(UTILS_SESSION_RANDOM_SECURITY_CODE, 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
