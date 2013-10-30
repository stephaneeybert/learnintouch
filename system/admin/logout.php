<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$adminUtils->closeSession();

$str = LibHtml::urlRedirect("$gAdminUrl/login.php");
printContent($str);
return;

?>
