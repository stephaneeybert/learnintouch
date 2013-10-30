<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);


$dynpageId = LibEnv::getEnvHttpGET("dynpageId");

$str = LibJavaScript::getJSLib();
//$str .= "<script src='$gJsUrl/utilities.js' type='text/javascript'></script>";
$str .= "\n<script type='text/javascript'>printPage();</script>";

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  $str .= $dynpageUtils->render($dynpage);
  }

print($str);

?>
