<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CONTACT);

$contactId = LibEnv::getEnvHttpGET("contactId");

// Delete from the garbage
$contactUtils->restoreFromGarbage($contactId);

$str = LibHtml::urlRedirect("$gContactUrl/garbage.php");
printMessage($str);
return;

?>
