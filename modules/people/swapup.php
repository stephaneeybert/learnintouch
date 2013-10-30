<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$mlText = $languageUtils->getMlText(__FILE__);

$peopleId = LibEnv::getEnvHttpGET("peopleId");

$peopleUtils->swapWithPrevious($peopleId);

$str = LibHtml::urlRedirect("$gPeopleUrl/admin.php");
printContent($str);
return;

?>
