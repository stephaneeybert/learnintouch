<?PHP

require_once("website.php");
require_once($gMailPath . "MailOutbox.php");
require_once($gMailPath . "MailOutboxDao.php");
require_once($gMailPath . "MailOutboxDB.php");
require_once($gMailPath . "MailOutboxUtils.php");

$nbUnsentMails = $mailOutboxUtils->countUnsent();

print($nbUnsentMails);

?>
