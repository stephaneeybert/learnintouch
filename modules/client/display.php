<?PHP

require_once("website.php");

$gTemplate->setPageContent($clientUtils->render());
require_once($gTemplatePath . "render.php");

?>
