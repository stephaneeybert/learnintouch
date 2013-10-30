<?PHP

require_once("website.php");

$peopleCategoryId = LibEnv::getEnvHttpGET("peopleCategoryId");
if (!$peopleCategoryId) {
  $peopleCategoryId = LibEnv::getEnvHttpPOST("peopleCategoryId");
}

// Prevent sql injection attacks as the id is always numeric
$peopleCategoryId = (int) $peopleCategoryId;

$preferenceUtils->init($peopleUtils->preferences);
$displayAll = $preferenceUtils->getValue("PEOPLE_DISPLAY_ALL");

if (!$displayAll) {
  if (!$peopleCategoryId) {
    $peopleCategoryId = LibSession::getSessionValue(PEOPLE_SESSION_CATEGORY);
  } else {
    LibSession::putSessionValue(PEOPLE_SESSION_CATEGORY, $peopleCategoryId);
  }
}

$str = $peopleCategoryUtils->render($peopleCategoryId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
