<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$elearningScoringId = LibEnv::getEnvHttpGET("elearningScoringId");
if (!$elearningScoringId) {
  $elearningScoringId = LibSession::getSessionValue(ELEARNING_SESSION_SCORING);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_SCORING, $elearningScoringId);
}

$help = $popupUtils->getHelpPopup($mlText[9], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->setHeader($mlText[0], "$gElearningUrl/scoring/admin.php");

if ($scoring = $elearningScoringUtils->selectById($elearningScoringId)) {
  $name = $scoring->getName();
  $panelUtils->addLine($panelUtils->addCell($mlText[8], "br"), $name, '', '', '');
  $panelUtils->addLine();
}

$strCommand = "<a href='$gElearningUrl/scoring/range/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($mlText[10], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

$elearningScoringRanges = $elearningScoringRangeUtils->selectByScoringId($elearningScoringId);

$panelUtils->openList();
foreach ($elearningScoringRanges as $elearningScoringRange) {
  $elearningScoringRangeId = $elearningScoringRange->getId();
  $upperRange = $elearningScoringRange->getUpperRange();

  $score = '';
  $advice = '';
  $proposal = '';
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageId = $language->getId();
    $languageCode = $language->getCode();
    $strImage = $languageUtils->renderImage($languageId);
    $score .= '<div>' . $strImage . ' : ' . $languageUtils->getTextForLanguage($elearningScoringRange->getScore(), $languageCode) . '</div>';
    $advice .= '<div>' . $strImage . ' : ' . $languageUtils->getTextForLanguage($elearningScoringRange->getAdvice(), $languageCode) . '</div>';
    $proposal .= '<div>' . $strImage . ' : ' . $languageUtils->getTextForLanguage($elearningScoringRange->getProposal(), $languageCode) . '</div>';
  }

  $linkText = $elearningScoringRange->getLinkUrl();
  $linkUrl = $elearningScoringRange->getLinkUrl();

  $webpageName = $templateUtils->getPageName($linkUrl);

  $strCommand = "<a href='$gElearningUrl/scoring/range/edit.php?elearningScoringRangeId=$elearningScoringRangeId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gElearningUrl/scoring/range/delete.php?elearningScoringRangeId=$elearningScoringRangeId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($upperRange, $score, $advice, $proposal, $webpageName, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
