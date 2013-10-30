<?PHP

require_once("website.php");

$str = $photoAlbumUtils->renderList();

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
