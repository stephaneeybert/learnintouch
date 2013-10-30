<?php

require_once("website.php");

$currentLanguageCode = LibEnv::getEnvHttpGET("currentLanguageCode");
$isAdmin = LibEnv::getEnvHttpGET("isAdmin");

$currentLanguageCode = urldecode($currentLanguageCode);

LibSession::openSession();

if ($isAdmin) {
  $languageUtils->setCurrentAdminLanguageCode($currentLanguageCode);

  $str = LibHtml::urlRedirect("$gAdminUrl/menu.php");
} else {
  $languageUtils->setCurrentLanguageCode($currentLanguageCode);

  if ($gIsPhoneClient) {
    $entryPageId = $templateUtils->getPhoneEntryPage($currentLanguageCode);
  } else {
    $entryPageId = $templateUtils->getComputerEntryPage($currentLanguageCode);
  }

  // Check for a url specified model
  // A model can be passed as an argument in the url to display a specific model
  // This is used in the navigation elements to switch to a model other than the default model
  $templateModelId = LibEnv::getEnvHttpGET("templateModelId");

  $url = $templateUtils->renderPageUrl($entryPageId, $templateModelId);

  $str = LibHtml::urlRedirect($url, 0);
}

printContent($str);

?>
