<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 400);
$panelUtils->setHelp($help);
$strCommand = " <a href='$gStatisticsUrl/resetCounter.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageReset' title='$mlText[5]'></a>"
  . " <a href='$gStatisticsUrl/setVisitDuration.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageTimer' title='$mlText[3]'></a>"
  . " <a href='$gStatisticsUrl/referer/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[8]'></a>"
  . " <a href='$gStatisticsUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[9]'></a>";
$panelUtils->addLine($panelUtils->addCell($strCommand, "nr"));

$counterDate = $clockUtils->systemToLocalNumericDate($statisticsVisitUtils->getCounterDate());
$nbVisitors = $statisticsVisitUtils->getCounterVisitors();
$nbVisits = $statisticsVisitUtils->getCounterVisits();

$str = "$mlText[6] $counterDate $mlText[2] $nbVisits $mlText[4] $nbVisitors $mlText[7]";
$panelUtils->addLine($panelUtils->addCell($str, "nc"));

$currentYearMonth = LibEnv::getEnvHttpPOST("currentYearMonth");

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderReferer($currentYearMonth), "nc"));

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderPageHits($currentYearMonth), "nc"));

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderMonths(), "nc"));

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderDays($currentYearMonth), "nc"));

if ($preferenceUtils->getValue("STATISTICS_DISPLAY_WEEKDAY")) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderWeekDays(), "nc"));
}

if ($preferenceUtils->getValue("STATISTICS_DISPLAY_HOUR")) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderHours(), "nc"));
}

if ($preferenceUtils->getValue("STATISTICS_DISPLAY_BROWSER")) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderBrowsers(), "nc"));

  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderPhones(), "nc"));
}

if ($preferenceUtils->getValue("STATISTICS_DISPLAY_ROBOT")) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderRobots(), "nc"));
}

if ($preferenceUtils->getValue("STATISTICS_DISPLAY_OS")) {
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($statisticsVisitUtils->renderOss(), "nc"));
}

$strRememberScroll = LibJavaScript::rememberScroll("statistics_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
