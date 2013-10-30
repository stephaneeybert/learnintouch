<?PHP

require_once("website.php");

$gTemplate->setPageContent($guestbookUtils->render());
require_once($gTemplatePath . "render.php");

?>
