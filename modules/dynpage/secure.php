<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");
$secure = LibEnv::getEnvHttpGET("secure");

$secure = LibString::cleanString($secure);

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  $dynpage->setSecured($secure);
  $dynpageUtils->update($dynpage);
}

$str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
printContent($str);
return;

?>
