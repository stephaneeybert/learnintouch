<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$str = $dynpageUtils->renderDirectoryTreeHeader(true);
$str .= $dynpageUtils->renderDirectoryTree();

print($str);

?>
