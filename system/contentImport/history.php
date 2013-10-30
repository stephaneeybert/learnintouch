<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$domainName = LibEnv::getEnvHttpPOST("domainName");
$contentType = LibEnv::getEnvHttpPOST("contentType");

if ($domainName) {
  $contentType = '';
}

define('CONTENT_COURSE', 'course');
define('CONTENT_LESSON', 'lesson');
define('CONTENT_EXERCISE', 'exercise');

$contentTypeList = array(
  '-1' => '',
  CONTENT_COURSE => $mlText[6],
  CONTENT_LESSON => $mlText[7],
  CONTENT_EXERCISE => $mlText[8],
);
$strSelectContentType = LibHtml::getSelectList("contentType", $contentTypeList, $contentType, true);

$contentImports = $contentImportUtils->selectImporting();
$domainNameList = Array('' => '');
foreach ($contentImports as $contentImport) {
  $domainName = $contentImport->getDomainName();
  $domainNameList[$domainName] = $domainName;
}
$strDomainName = LibHtml::getSelectList("domainName", $domainNameList, $domainName, true);

$panelUtils->setHeader($mlText[0], "$gContentImportUrl/importers/admin.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 200);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strDomainName, 'n'), '');
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $panelUtils->addCell($strSelectContentType, 'n'), '');
$panelUtils->closeForm();
$panelUtils->addLine();

$panelUtils->addLine($panelUtils->addCell($mlText[1], "nb"), $panelUtils->addCell($mlText[2], "nb"), $panelUtils->addCell($mlText[3], "nb"));

$preferenceUtils->init($elearningExerciseUtils->preferences);
$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($domainName) {
  $contentImportHistories = $contentImportHistoryUtils->selectByDomainName($domainName);
} else if ($contentType == CONTENT_COURSE) {
  $contentImportHistories = $contentImportHistoryUtils->selectContentCourse();
} else if ($contentType == CONTENT_LESSON) {
  $contentImportHistories = $contentImportHistoryUtils->selectContentLesson();
} else if ($contentType == CONTENT_EXERCISE) {
  $contentImportHistories = $contentImportHistoryUtils->selectContentExercise();
} else {
  $contentImportHistories = $contentImportHistoryUtils->selectAll();
}

$listNbItems = $contentImportHistoryUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

foreach ($contentImportHistories as $contentImportHistory) {
  $domainName = $contentImportHistory->getDomainName();
  $course = $contentImportHistory->getCourse();
  $lesson = $contentImportHistory->getLesson();
  $exercise = $contentImportHistory->getExercise();
  $importDateTime = $contentImportHistory->getImportDateTime();

  $panelUtils->addLine($domainName, $course, $importDateTime);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
