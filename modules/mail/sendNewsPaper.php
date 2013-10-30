<?PHP

require_once("website.php");
require_once($gMailPath . "MailOutbox.php");
require_once($gMailPath . "MailOutboxDao.php");
require_once($gMailPath . "MailOutboxDB.php");
require_once($gMailPath . "MailOutboxUtils.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  require_once($gMailPath . "sendController.php");

} else {

  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

  if (!$newsPaperId) {
    $newsPaperId = LibSession::getSessionValue(MAIL_SESSION_MAIL);
  } else {
    LibSession::putSessionValue(MAIL_SESSION_MAIL, $newsPaperId);
  }

  $title = '';
  $releaseDate = '';
  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $title = $newsPaper->getTitle();
    $releaseDate = $newsPaper->getReleaseDate();
  }

  $releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

  $subject = $title . ' ' . $releaseDate;
  $body = $newsPaperUtils->render($newsPaperId);
  $textFormat = '';
  $strAttachment = '';

  $strPreview = "$gNewsUrl/newsPaper/preview.php?newsPaperId=$newsPaperId";

  require_once($gMailPath . "sendView.php");

}

?>
