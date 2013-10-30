<?PHP

require_once("website.php");

$gTemplate->setPageContent("The url is invalid!");
$gTemplate->setPageTitle("Invalid");

require_once($gTemplatePath . "render.php");

?>
