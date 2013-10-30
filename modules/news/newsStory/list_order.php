<?PHP

require_once("website.php");

LibHtml::preventCaching();


$newsStoryIds = LibEnv::getEnvHttpPOST("newsStoryIds");

$listOrder = 1;
foreach ($newsStoryIds as $newsStoryId) {
  // An ajax request parameter value is UTF-8 encoded
  $newsStoryId = utf8_decode($newsStoryId);

  if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
    $newsStory->setListOrder($listOrder);
    $newsStoryUtils->update($newsStory);
    $listOrder++;
  }
}

?>
