<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_STATISTICS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $statisticsRefererId = LibEnv::getEnvHttpPOST("statisticsRefererId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $url = LibEnv::getEnvHttpPOST("url");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $url = LibString::cleanString($url);

  // The url is required
  if (!$url) {
    array_push($warnings, $mlText[4]);
    }

  // Validate the url
  if ($url && LibUtils::isInvalidUrl($url)) {
    array_push($warnings, $mlText[21]);
    }

  // Format the url
  $url = LibUtils::formatUrl($url);

  if (count($warnings) == 0) {

  if ($statisticsReferer = $statisticsRefererUtils->selectById($statisticsRefererId)) {
    $statisticsReferer->setName($name);
    $statisticsReferer->setDescription($description);
    $statisticsReferer->setUrl($url);
    $statisticsRefererUtils->update($statisticsReferer);
    } else {
    $statisticsReferer = new StatisticsReferer();
    $statisticsReferer->setName($name);
    $statisticsReferer->setDescription($description);
    $statisticsReferer->setUrl($url);
    $statisticsRefererUtils->insert($statisticsReferer);
    }

  $str = LibHtml::urlRedirect("$gStatisticsUrl/referer/admin.php");
  printContent($str);
  return;

  }

  } else {

  $statisticsRefererId = LibEnv::getEnvHttpGET("statisticsRefererId");

  $name = '';
  $description = '';
  $url = '';
  if ($statisticsRefererId) {
    if ($statisticsReferer = $statisticsRefererUtils->selectById($statisticsRefererId)) {
      $name = $statisticsReferer->getName();
      $description = $statisticsReferer->getDescription();
      $url = $statisticsReferer->getUrl();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gStatisticsUrl/referer/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='url' value='$url' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('statisticsRefererId', $statisticsRefererId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
