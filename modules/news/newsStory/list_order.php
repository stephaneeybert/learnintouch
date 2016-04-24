<?PHP

require_once("website.php");

LibHtml::preventCaching();


$newsStoryIds = LibEnv::getEnvHttpPOST("newsStoryIds");

$listOrder = 1;
foreach ($newsStoryIds as $newsStoryId) {
  if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
    $newsStory->setListOrder($listOrder);
    $newsStoryUtils->update($newsStory);
    $listOrder++;
  }
}

?>
