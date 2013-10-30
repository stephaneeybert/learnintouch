<?PHP

require_once("website.php");

LibHtml::preventCaching();

$mlText = $languageUtils->getMlText(__FILE__);

$lexiconEntryId = LibEnv::getEnvHttpGET("lexiconEntryId");
$pageUrl = LibEnv::getEnvHttpGET("pageUrl");

if (!$lexiconEntryId) {
  return;
}

// Ajax treats its data as UTF-8
$pageUrl = utf8_decode($pageUrl);

if (!$lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
  $websiteName = $profileUtils->getProfileValue("website.name");
  $websiteEmail = $profileUtils->getProfileValue("website.email");
  $emailSubject = $mlText[0];
  $emailBody = $mlText[1] . ' ' . $lexiconEntryId . ' ' . $mlText[2] 
    . "\n\n" . $mlText[3] . "\n\n<a href='$pageUrl'>" . $pageUrl . "</a>"
    . "\n\n" . $mlText[4] . " " . LEXICON_ENTRY_DOM_ID_PREFIX . $lexiconEntryId
    . "\n\n" . $websiteName;
  $emailBody = nl2br($emailBody);
  LibEmail::sendMail($websiteEmail, $websiteName, $emailSubject, $emailBody, $websiteEmail, $websiteName);
}

?>
