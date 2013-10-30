<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mailId = LibEnv::getEnvHttpGET("mailId");
$locked = LibEnv::getEnvHttpGET("locked");

$adminLogin = $adminUtils->checkAdminLogin();
if ($adminUtils->isSuperAdmin($adminLogin)) {
  if ($mail = $mailUtils->selectById($mailId)) {
    $mail->setLocked($locked);
    $mailUtils->update($mail);
  }
}

$str = LibHtml::urlRedirect("$gMailUrl/admin.php");
printContent($str);
return;

?>
