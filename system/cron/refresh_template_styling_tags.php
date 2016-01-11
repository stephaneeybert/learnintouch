<?PHP

$specific = '';
if ($argc == 2) {
  $specific = $argv[1];
} else {
  die("Some arguments are missing for the file $PHP_SELF");
}

if (!is_file($specific)) {
  die("The file $specific is missing for the file $PHP_SELF");
}
include($specific);

require_once("cli.php");

// Update the urls
// Create the tags for the elements or the system pages
// This is required only if the code source has been modified
// and some new tags have beed added in the rendering
// So it should rarely be required

$templateElements = $templateElementUtils->selectAll();
foreach ($templateElements as $templateElement) {
  $templateElementId = $templateElement->getId();
  $elementType = $templateElement->getElementType();
  $objectId = $templateElement->getObjectId();
  $content = $templateElementUtils->renderContent($templateElementId, $elementType, $objectId);
  $tagIDs = $templateElementUtils->getTagIDs($templateElementId, $content);
  $templateElementUtils->createTags($templateElementId, $tagIDs);
}

$templatePages = $templatePageUtils->selectAll();
foreach ($templatePages as $templatePage) {
  $templatePageId = $templatePage->getId();
  $systemPage = $templatePage->getSystemPage();
  $content = $templatePageTagUtils->renderSystemPageContent($systemPage);
  $tagIDs = $templatePageUtils->getTagIDs($templatePageId, $content);
  if (is_array($tagIDs)) {
    $templatePageUtils->createTags($templatePageId, $tagIDs);
    $templatePageTagUtils->cleanupDatabaseTagIDs($templatePageId, $tagIDs);
  }
}

?>
