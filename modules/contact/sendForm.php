<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

// Get the post variables
$formVariables = array();
foreach ($_POST as $key => $value) {
  $formVariables[$key] = $value;
  }
foreach ($_GET as $key => $value) {
  $formVariables[$key] = $value;
  }

$strFormVariables = '';
foreach ($formVariables as $key => $value) {
  $value = LibString::cleanString($value);

  if ($key != 'contactRecipientEmail' && $key != 'contactAcknowledgementMessage') {
    $strFormVariables .= "<br>$key : $value";
    }
  }


// Send an email with all the posted variables
$websiteName = $profileUtils->getProfileValue("website.name");
$siteEmail = $profileUtils->getProfileValue("website.email");

// Get the recipient email address
$preferenceUtils->init($contactUtils->preferences);

// Get a custom recipient email address
$recipientEmail = LibEnv::getEnvHttpGET("contactRecipientEmail");

// If no specific recipient has been specified then use the website email address
if (!$recipientEmail) {
  $recipientEmail = $preferenceUtils->getValue("CONTACT_FORM_EMAIL_ADDRESS");
  }
if (!$recipientEmail) {
  $recipientEmail = $siteEmail;
  }

$recipientName = $websiteName;
if (!$recipientName) {
  $recipientName = $recipientEmail;
  }

$emailSubject = "$websiteText[1] $recipientName";

$emailBody = "$websiteText[2] $recipientName "
  . "<br /><br />"
  . $websiteText[3]
  . "<br />"
  . $strFormVariables
  . "<br /><br />"
  . $websiteText[4]
  . "<br /><br />"
  . $recipientName;

$senderEmail = '';
foreach ($formVariables as $key => $value) {
  if ($key != 'contactRecipientEmail' && $key != 'contactAcknowledgementMessage') {
    if (LibEmail::validate($value)) {
      $senderEmail = $value;
      }
    }
  }

if ($senderEmail) {
  // Register the message
  $contactUtils->registerMessage($senderEmail, $emailSubject, $emailBody);
  } else {
  // Send the form message directly to the contact or website email address
  if (LibEmail::validate($recipientEmail)) {
    LibEmail::sendMail($recipientEmail, $recipientName, $emailSubject, $emailBody, $siteEmail, $recipientName);
    }
  }

// Get a custom acknowledgement message
$acknowledge = '';
$acknowledge = LibEnv::getEnvHttpGET("contactAcknowledgementMessage");
if (!$acknowledge) {
  $acknowledge = $preferenceUtils->getValue("CONTACT_ACKNOWLEDGE");
  }
if (!$acknowledge) {
  $acknowledge = $websiteText[0];
  }

$acknowledgementPage = $preferenceUtils->getValue("CONTACT_ACKNOWLEDGEMENT_PAGE");
if ($acknowledgementPage) {
  $url = $templateUtils->renderPageUrl($acknowledgementPage);
  $str = LibHtml::urlRedirect($url);
  } else {
  $str = "\n<div class='system'>"
    . "\n<div class='system_comment'>"
    . $acknowledge
    . "</div>"
    . "\n</div>";
  }

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
