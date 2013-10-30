<?PHP
require_once ("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv :: getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSessionId = LibEnv :: getEnvHttpPOST("elearningSessionId");

  // Check that there are no subscriptions on the session
  if ($elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionId($elearningSessionId)) {
    array_push($warnings, $mlText[5]);
  }

  if ($elearningSessionCourses = $elearningSessionCourseUtils->selectBySessionId($elearningSessionId)) {
    array_push($warnings, $mlText[6]);
    $strRedirect = "<a href='$gElearningUrl/session/courses.php?elearningSessionId=$elearningSessionId' $gJSNoStatus>" . $mlText[8] . "</a>";
    array_push($warnings, $strRedirect);
  }

  if (count($warnings) == 0) {

    $elearningSessionUtils->deleteSession($elearningSessionId);

    $str = LibHtml :: urlRedirect("$gElearningUrl/session/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningSessionId = LibEnv :: getEnvHttpGET("elearningSessionId");

}

$name = '';
$openDate = '';
$closeDate = '';
if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
  $elearningSessionId = $elearningSession->getId();
  $name = $elearningSession->getName();
  $openDate = $elearningSession->getOpenDate();
  $closeDate = $elearningSession->getCloseDate();
}

$openDate = $clockUtils->systemToLocalNumericDate($openDate);
$closeDate = $clockUtils->systemToLocalNumericDate($closeDate);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/session/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "$name $mlText[3] $openDate $mlText[4]
  $closeDate");
$panelUtils->addLine();
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningSessionId', $elearningSessionId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
