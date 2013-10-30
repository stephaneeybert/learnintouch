<?PHP

require_once("website.php");

$documentCategoryId = LibEnv::getEnvHttpGET("documentCategoryId");
if (!$documentCategoryId) {
  $documentCategoryId = LibEnv::getEnvHttpPOST("documentCategoryId");
}

// Prevent sql injection attacks as the id is always numeric
$documentCategoryId = (int) $documentCategoryId;

$str = $documentCategoryUtils->render($documentCategoryId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
