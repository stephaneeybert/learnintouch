<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$smsId = LibEnv::getEnvHttpGET("smsId");


$str = '';

if ($sms = $smsUtils->selectById($smsId)) {
  $str = "<div style='background-color:#ffffff; padding: 4px;'>" . $smsUtils->renderBody($sms) . "</div>";
}

print($str);

?>
